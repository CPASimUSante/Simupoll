/**
 * SimupollCreatorApp
 */
import questionsListDirective from './Directives/questionsListDirective'
import propositionsListDirective from './Directives/propositionsListDirective'
import simupollCreatorDirective from './Directives/simupollCreatorDirective'
import simupollCreatorController from './Controllers/SimupollCreatorController'

//import classes called in dependency injection
angular
.module('SimupollCreatorApp', [
    'ngSanitize',
    'ngRoute',
    'ui.bootstrap',
    'ui.tinymce',
    'ui.translation'
])
.controller('SimupollCreatorController', [
//    'periodModal',
    simupollCreatorController
])
//displays the propositions loop
.directive('propositionsList', () => new propositionsListDirective)
//displays the questions loop
.directive('questionsList', () => new questionsListDirective)
//main directive
.directive('simupollCreator', () => new simupollCreatorDirective('SimupollCreatorController'))
