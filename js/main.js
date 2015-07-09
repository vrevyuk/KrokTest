/**
 * Created by vitaly on 01.07.15.
 */

var kroktest = {
  // constants
  VERTICAL: 0,
  HORIZONTAL: 1,

  //variables
  demoMode: false,
  accessCode: null,
  accessCodeID: null,
  email: null,
  orientation: null,
  questions: [],

  //methods
  init: function() {
      //window.localStorage.clear();
      this.loadStorage();
  },

    loadStorage: function() {
        var storage = window.localStorage;
        if(storage) {
            this.accessCode = storage.getItem('accessCode') || null;
            this.accessCodeID = storage.getItem('accessCodeID') || null;
            this.email = storage.getItem('email') || null;
        }
    },

    saveStorage: function (key, value) {
        var storage = window.localStorage;
        if(storage) {
            storage.setItem(key, value);
            this[key] = value;
        }
    },

  next: function () {
      if(this.accessCode) {
          this.requestCode(this.accessCode)
      } else {
          $.mobile.changePage('#enterCode');
      }
  },

  requestCode: function(code) {
      $.ajax({
          method: 'get',
          url: 'db/request_code.php?code='+code,
          dataType: 'json',
          krok: {obj: this},
          async: 'true',
          success: function (res) {
              if(res.status) {
                  var krok = this.krok.obj;
                  krok.saveStorage('accessCode', res.code);
                  krok.saveStorage('accessCodeID', res.code_id);
                  krok.saveStorage('email', res.email);
                  $.mobile.changePage('#summaryPage');
              } else {
                  alert(res.message);
                  $('#code').val('');
              }
          },
          error: function (data, status, errorThrown) {
              alert(status);
          }
      });
  },

  demo: function() {
      this.demoMode = true;
      $.mobile.changePage('#summaryPage');
  },

  go: function () {
      $('#goHeader').html($('#category option:selected').text());
      $.mobile.changePage('#goPage');
      kroktest.getListQuestions($('#category').val());
  },

  statistic: function() {
      $.ajax({
          method: 'get',
          url: 'db/statistic.php?reg_id="' + kroktest.accessCodeID + '"',
          dataType: 'json',
          async: 'true',
          success: function (result) {
              if(result.status) {
                  var list = result.result;
                  var tbody = $('#statisticTable');
                  tbody.empty();
                  list.forEach(function(item, i, list) {
                      tbody.append('<tr><td>'+item['catname']+'</td><td>'+item['success']+'</td><td>'+item['failed']+'</td></tr>');
                  });
                  tbody.floatThead();
              } else { alert(result.message); }
          },
          error: function (data, status, errorThrown) {
              //alert(status);
          }
      });
  },

  loadCategories: function(codeID) {
      $.ajax({
          method: 'get',
          url: 'db/categories.php?codeID=' + codeID + '"',
          dataType: 'json',
          async: 'true',
          success: function (result) {
              if(result.status) {
                  var categories = $('#category');
                  for(var i=0; i< result.result.length; i++) {
                      categories.append('<option value="' + result.result[i].id + '">' + result.result[i].catname + '</option>');
                  }
                  categories.selectmenu('refresh');

              } else { alert(result.message); }
          },
          error: function(data, status, errorThrown) {
              alert(status);
          }
      });
  },

  getListQuestions: function(catid) {
    $.ajax({
        method: 'get',
        url: 'db/questions.php?catid='+catid,
        dataType: 'json',
        async: 'true',
        success: function (result) {
            if(result.status) {
                kroktest.questions = result.result;
                kroktest.nextQuestion(catid);
            } else { alert(result.message); }
        },
        error: function (data, status, errorThrown) {
            alert(status);
        }
    });
  },

  nextQuestion: function (catid) {
      $('#goPageCircleBtn').empty();
      var rndPos = kroktest.random(kroktest.questions.length-1);
      var res = kroktest.questions[rndPos];
      var goContent = $('#goPageContent');
      goContent.empty();
      if(res) {
          goContent.append('<span>' + res.question + '</span>');
          for(var i=1; i<=5; i++) {
              if(res['answer'+i].length > 0) {
                  goContent.append('<a href="#" id="answer'+i+'" class="ui-btn ui-mini ui-corner-all" onclick="kroktest.checkAnswer(this, '+rndPos+', '+catid+')">' + res['answer'+i] + '</a>');
              }
          }
          goContent.trigger('refresh');
          goContent.removeClass('ui-disabled');
      }
  },

  checkAnswer: function(btn, position, catid) {
      var success = kroktest.questions[position].success;
      var failAnswer;
      var number = btn.id.replace('answer', '');
      if(number == success) {
          btn.style.backgroundColor = 'green';
          btn.className += ' ui-btn-icon-left ui-icon-check';
          failAnswer = true;
      } else {
          btn.style.backgroundColor = 'red';
          btn.className += ' ui-btn-icon-left ui-icon-delete';
          var hit = $('#answer'+success);
          hit.css('background', 'green').css('color', 'white').css('text-shadow', 'none');
          failAnswer = false;
      }
      btn.style.color = 'white';
      btn.style.textShadow = 'none';
      $('#goPageContent').addClass('ui-disabled');

      if(failAnswer) {
          $('#goPageCircleBtn').append('<div class="circle-green-btn" onclick="kroktest.nextQuestion('+catid+')">NEXT</div>');
      } else {
          $('#goPageCircleBtn').append('<div class="circle-red-btn" onclick="kroktest.nextQuestion('+catid+')">NEXT</div>');
      }
      this.saveStatistic(catid, failAnswer);
  },

  saveStatistic: function(catid, result) {
      $.ajax({
          method: 'get',
          url: 'db/save_stat.php?reg='+this.accessCodeID+'&catid='+catid+'&result='+result,
          dataType: 'json',
          async: 'true',
          success: function (rslt) {
;          },
          error: function (data, status, errorThrown) {
          }
      });
  },

  random: function(max) {
      return Math.floor(Math.random() * (max - 0 + 1));
  }
};

$(document).bind('ready', function() {
    kroktest.init();
    $('#summaryPage').bind('pagebeforeshow', function () {
        kroktest.loadCategories(kroktest.accessCodeID);
        kroktest.statistic(kroktest.accessCodeID);
    });
});

