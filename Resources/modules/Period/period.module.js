/**
 * PeriodApp
 */

import angular from 'angular/index'
//import classes called in dependency injection
//for $uibModal
import {} from 'angular-bootstrap'
import moment from 'moment/moment'
//import {} from 'bootstrap-datepicker'
//import {} from 'bootstrap-daterangepicker'

import periodTemplate from './Partials/period.directive.html'
import periodDirective from './Directives/periodDirective'
import periodService from './Services/PeriodService'
import periodController from './Controllers/PeriodController'

//import classes called in dependency injection
// Period module
angular
    .module('PeriodApp', [
        'ui.bootstrap'
    ])
    .service('PeriodService', [
        '$http',
        '$q',
        // 'moment',
        periodService
    ])
    //modal management
    .factory('periodModal', [
        '$uibModal',
        $modal => ({
            open: template => $modal.open({ template })
        })
      ])
      //format date
     .filter('dateFormat', function($filter) {
          return function(input) {
              if (input === null){ return "" }
console.log(input);
              if (input.hasOwnProperty('date')){
                  return $filter('date')(new Date(input.date), 'dd/MM/yyyy')
              } else {
                  return new Date(input).toISOString().split('T')[0];
              }
          }
      })
    .controller('PeriodController', [
        'PeriodService',
        'periodModal',
        'dateFormatFilter',
        periodController
    ])
    .directive('period', () => new periodDirective('PeriodController', periodTemplate))
    //translations
    .filter('trans', () => (string, domain = 'platform') =>
        Translator.trans(string, domain)
    )
    //TODO : remove before merge
    .filter('debug', function() {
      return function(input) {
        if (input === '') return 'empty string';
        return input ? input : ('' + input);
      };
    })
