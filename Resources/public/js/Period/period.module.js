/**
 * PeriodApp
 */

import angular from 'angular/index'
//import classes called in dependency injection
//for $uibModal
import {} from 'angular-bootstrap'
import {} from 'bootstrap-datepicker'
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
        periodService
    ])
    //modal management
    .factory('periodModal', [
        '$uibModal',
        $modal => ({
            open: template => $modal.open({ template })
        })
      ])
    .controller('PeriodController', [
        'PeriodService',
        'periodModal',
        periodController
    ])
    .directive('period', () => new periodDirective('PeriodController', periodTemplate))
    //translations
    .filter('trans', () => (string, domain = 'platform') =>
        Translator.trans(string, domain)
    )
    //format date 
    .filter('dateFormat', function($filter) {
        return function(input) {
            if(input == null){ return "" }
            var _date = $filter('date')(new Date(input), 'dd/MM/yyyy')
            return _date.toUpperCase()
        }
    })
