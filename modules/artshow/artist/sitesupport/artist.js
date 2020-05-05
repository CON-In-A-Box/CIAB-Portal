/*
 * Javacript for the Artist page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, showSpinner, hideSpinner, userProfile, userLookup */

var artistPage = (function(options) {
  'use strict';

  var artists;
  var configuration;

  var settings = Object.assign(
    {
      debug: true
    }, options);

  return {
    options: function(opts) {
      settings = Object.assign(settings, opts);
    },

    debugmsg: function(message) {
      if (settings.debug) {
        var target = document.getElementById('headline_section');
        target.classList.add('UI-show');
        target.innerHTML = message;
      }
    },

    load: function() {
      apiRequest('GET',
        'artshow/',
        'maxResults=all')
        .then(function(response) {
          var params = new URLSearchParams(window.location.search);
          if (params.has('accountId')) {
            var accountId = params.get('accountId');
          }
          var result = JSON.parse(response.responseText);
          configuration = result.data;
          artistPage.debugmsg(response.responseText);
          artistPage.initializePage();
          artistPage.loadData(null, accountId);
        });
    },

    loadData: function(selectedIndex, accountId) {
      showSpinner();
      document.getElementById('artist_list').options.length = 0;

      apiRequest('GET',
        'artshow/artists',
        'maxResults=all&include=id,AccountID')
        .then(function(response) {
          hideSpinner();
          artists = JSON.parse(response.responseText).data;
          var select = document.getElementById('artist_list');
          select.selectedIndex = -1;
          artists.forEach(function(data, index) {
            if (data !== null) {
              var name;
              if (data.id.CompanyName &&
                  data.id.CompanyNameOnSheet == '1') {
                name = data.id.CompanyName;
              } else {
                name = data.id.AccountID.firstName + ' ' +
                    data.id.AccountID.lastName;
              }
              var option = document.createElement('OPTION');
              option.text = name;
              option.value = index;
              select.add(option);
              if (typeof accountId !== 'undefined' &&
                  data.id.AccountID.id == accountId) {
                select.selectedIndex =  index;
                artistPage.selectArtist(artists[index]);
              }
            }
          });
          if (typeof selectedIndex !== 'undefined' &&
              selectedIndex !== null) {
            select.selectedIndex =  selectedIndex;
          }
        })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
          hideSpinner();
          artistPage.debugmsg(response.responseText);
        });
    },

    initializePage: function() {
      var mailReturn = document.getElementById('reg-return-type');
      configuration.ReturnMethod.value.forEach(function(x) {
        var option = document.createElement('option');
        option.text = x;
        mailReturn.add(option);
      });
      mailReturn.selectIndex = 0;
      var questionSection = document.getElementById('artist_event_questions');
      configuration.RegistrationQuestion.value.forEach(function(x) {
        var id = 'custom_question_' + x.QuestionID;
        var label = document.createElement('LABEL');
        label.classList.add('UI-label');
        label.setAttribute('for', id);
        label.innerHTML = x.Text;

        var input = document.createElement('INPUT');
        input.classList.add('UI-margin');
        input.id = id;
        if (x.BooleanQuestion == '1') {
          input.setAttribute('type', 'checkbox');
          input.classList.add('UI-checkbox');
          label.classList.add('UI-padding');
          input.setAttribute('onchange', 'artistPage.artistDataChange();');
          questionSection.appendChild(input);
          questionSection.appendChild(label);
          questionSection.appendChild(document.createElement('BR'));
        } else {
          input.setAttribute('onchange', 'artistPage.artistDataChange();');
          input.classList.add('UI-input');
          input.classList.add('UI-margin');
          questionSection.appendChild(label);
          questionSection.appendChild(input);
        }
      });
    },

    selectArtist: function(artist) {
      if (typeof artist == 'undefined') {
        var select = document.getElementById('artist_list');
        artist = artists[select.options[select.selectedIndex].value];
      }

      artistPage.newArtist(false);

      userProfile.populate(artist.id.AccountID);

      document.getElementById('company').value = artist.id.CompanyName;
      if (artist.id.CompanyNameOnPayment == '1')
      {document.getElementById('use_company_check').checked = true;}
      else
      {document.getElementById('use_company_check').checked = false;}
      if (artist.id.CompanyNameOnSheet == '1')
      {document.getElementById('use_company').checked = true;}
      else
      {document.getElementById('use_company').checked = false;}
      document.getElementById('website').value = artist.id.Website;
      if (artist.id.Professional == '1')
      {document.getElementById('professional').checked = true;}
      else
      {document.getElementById('amateur').checked = true;}

      showSpinner();
      apiRequest('GET', 'artshow/artist/' + artist.id.ArtistID + '/show',
        null)
        .then(function(response) {
          document.getElementById('event_details_present').value = '1';
          artistPage.debugmsg(response.responseText);

          var data = JSON.parse(response.responseText);
          document.getElementById('reg_mailin').checked = (data.MailIn == '1');
          document.getElementById('reg-return-type').value = data.ReturnMethod;

          configuration.RegistrationQuestion.value.forEach(function(x) {
            var id = 'custom_question_' + x.QuestionID;
            if (id in data) {
              if (x.BooleanQuestion == '1') {
                document.getElementById(id).checked = (data[id] == '1');
              } else {
                document.getElementById(id).value = data[id];
              }
            }
          });
          hideSpinner();
          document.getElementById('artist_art').disabled = false;
        })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
          artistPage.debugmsg(response.responseText);
          document.getElementById('event_details_present').value = '0';
          hideSpinner();
        });

    },

    newArtist: function(clearIndex) {
      document.getElementById('artist_art').disabled = true;
      userProfile.clear();
      if (clearIndex) {
        document.getElementById('artist_list').selectedIndex = -1;
      }
      document.getElementById('company').value = '';
      document.getElementById('use_company_check').checked = false;
      document.getElementById('use_company').checked = false;
      document.getElementById('website').value = '';
      document.getElementById('amateur').checked = true;
      document.getElementById('reg_mailin').checked = false;
      document.getElementById('reg-return-type').selectedIndex = 0;
      document.getElementById('event_details_present').value = '0';
      configuration.RegistrationQuestion.value.forEach(function(x) {
        var id = 'custom_question_' + x.QuestionID;
        if (x.BooleanQuestion == '1') {
          document.getElementById(id).checked = false;
        } else {
          document.getElementById(id).value = '';
        }
      });
      document.getElementById('save_artist_button').disabled = true;
    },

    artistDataChange: function() {
      if (userProfile.getElementById('email1').value != '' &&
            (userProfile.getElementById('firstName').value  !== '' ||
             userProfile.getElementById('lastName').value !== ''))
      {
        document.getElementById('save_artist_button').disabled = false;
      } else {
        document.getElementById('save_artist_button').disabled = true;
      }
    },

    saveArtist: function() {
      var i = document.getElementById('artist_list').selectedIndex;
      showSpinner();
      userProfile.updateAccount()
        .then(function(response) {
          var data = JSON.parse(response.responseText);
          var aid = data.id;
          var method = 'POST';
          var uri = 'artshow/artist';
          var body = [ 'AccountID=' + aid ];
          if (i >= 0) {
            method = 'PUT';
            uri = 'artshow/artist/' + artists[i].id.ArtistID;
            body = [];
          }
          var bid = (document.getElementById('use_company').checked ? 1 : 0);
          var pay =
            (document.getElementById('use_company_check').checked ? 1 : 0);
          var prof = (document.getElementById('professional').checked ? 1 : 0);
          if (document.getElementById('company').value.length > 0) {
            body.push('CompanyName=' +
              document.getElementById('company').value);
          }
          if (document.getElementById('website').value.length > 0) {
            body.push('Website=' + document.getElementById('website').value);
          }
          body.push('Website=' + document.getElementById('website').value);
          body.push('CompanyNameOnSheet=' + bid);
          body.push('CompanyNameOnPayment=' + pay);
          body.push('Professional=' + prof);
          apiRequest(method, uri, body.join('&'))
            .then(function(response) {
              body = [];
              var artist = JSON.parse(response.responseText);
              body.push('MailIn=' +
                (document.getElementById('reg_mailin').checked ? 1 : 0));
              var mailtype  = document.getElementById('reg-return-type').value;
              if (mailtype) {
                body.push('ReturnMethod=' + mailtype);
              }

              var custom = '';
              configuration.RegistrationQuestion.value.forEach(function(x) {
                var id = 'custom_question_' + x.QuestionID;
                var answer = '';
                if (x.BooleanQuestion == '1') {
                  answer = (document.getElementById(id).checked ? 1 : 0);
                } else {
                  answer = document.getElementById(id).value;
                  if (answer == null) {
                    answer = '';
                  }
                }
                custom = custom + '&' + id + '=' + answer;
              });

              if (document.getElementById('event_details_present').value == '1')
              {
                method = 'PUT';
              } else {
                method = 'POST';
              }

              apiRequest(method, 'artshow/artist/' + artist.ArtistID +
                '/show',
              body.join('&') + '&' + custom)
                .then(function() {
                  artistPage.loadData(i);
                })
                .catch(function(response) {
                  if (response instanceof Error) { throw response; }
                })
                .finally(function() {
                  hideSpinner();
                });
            })
            .catch(function(response) {
              if (response instanceof Error) { throw response; }
              hideSpinner();
            });
        })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
          hideSpinner();
        });

    },

    lookup: function(origin, item) {
      document.getElementById('userLookup_dropdown').classList.add('UI-hide');
      artistPage.newArtist(true);
      setTimeout(function() {
        userLookup.clear();
      }, 3000);
      var found = null;
      artists.forEach(function(artist, index) {
        if (found == null &&
            parseInt(artist.id.AccountID.id) == parseInt(item.Id)) {
          document.getElementById('artist_list').selectedIndex = index;
          found = index;
        }
      });
      if (found !== null) {
        artistPage.selectArtist(artists[found]);
      } else {
        showSpinner();
        apiRequest('GET', 'member/' + item.Id, null)
          .then(function(response) {
            hideSpinner();
            var data = JSON.parse(response.responseText);
            userProfile.populate(data);
            document.getElementById('save_artist_button').disabled = false;
          })
          .catch(function(response) {
            if (response instanceof Error) { throw response; }
            artistPage.debugmsg(response.responseText);
            hideSpinner();
          });
      }
    },

    artistArt: function() {
      var i = document.getElementById('artist_list').selectedIndex;
      window.location = 'index.php?Function=artshow/art&artistId=' +
        artists[i].id.ArtistID;
    }

  };
})();

if (window.addEventListener) {
  window.addEventListener('load', artistPage.load);
} else {
  window.attachEvent('onload', artistPage.load);
}
