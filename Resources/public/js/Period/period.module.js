/**
 * PeriodApp
 */
import periodDirective from './Directives/periodDirective'
import periodController from './Controllers/PeriodController'

//import classes called in dependency injection
angular
.module('PeriodApp', [
    'ngSanitize',
    'ngRoute',
    'ui.bootstrap',
    'ui.translation'
])
.controller('PeriodController', [
//    'periodModal',
    periodController
])
.directive('period', () => new periodDirective('PeriodController'))
