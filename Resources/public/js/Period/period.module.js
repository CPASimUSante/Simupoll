/**
 * PeriodApp
 */

import angular from 'angular/index'
//import classes called in dependency injection
//for $uibModal
import {} from 'angular-bootstrap'
import {} from 'bootstrap-datepicker'
//import {} from 'bootstrap-daterangepicker'

import periodDirective from './Directives/periodDirective'
import periodController from './Controllers/PeriodController'

//import classes called in dependency injection
// Period module
angular
    .module('PeriodApp', [
        'ui.bootstrap'
    ])
    //modal management
    .factory('periodModal', [
        '$uibModal',
        $modal => ({
            open: template => $modal.open({ template })
        })
      ])
    .controller('PeriodController', [
        'periodModal',
        periodController
    ])
    .directive('period', () => new periodDirective('PeriodController'))
    //translations
    .filter('trans', () => (string, domain = 'platform') =>
        Translator.trans(string, domain)
    )
