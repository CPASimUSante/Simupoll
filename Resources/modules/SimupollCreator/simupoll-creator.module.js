/**
 * SimupollCreatorApp
 */

import angular from 'angular/index'
//import classes called in dependency injection
//for $uibModal
import {} from 'angular-bootstrap'
import {} from 'angular-ui-tinymce'

import questionsListTemplate from './Partials/questions-list.html'
import questionsListDirective from './Directives/questionsListDirective'
import propositionsListTemplate from './Partials/propositions-list.html'
import propositionsListDirective from './Directives/propositionsListDirective'
import simupollCreatorTemplate from './Partials/simupollCreator.directive.html'
import simupollCreatorDirective from './Directives/simupollCreatorDirective'
import simupollCreatorController from './Controllers/SimupollCreatorController'
import simupollCreatorService from './Services/SimupollCreatorService'

//import classes called in dependency injection
angular
    .module('SimupollCreatorApp', [
        'ui.bootstrap',
        'ui.tinymce'
    ])
    .service('SimupollCreatorService', [
        '$http',
        '$q',
        simupollCreatorService
    ])
    //modal management
    .factory('simupollCreatorModal', [
        '$uibModal',
        $modal => ({
            open: template => $modal.open({ template })
        })
      ])
    .controller('SimupollCreatorController', [
        'SimupollCreatorService',
        'simupollCreatorModal',
        simupollCreatorController
    ])
    //displays the propositions loop
    .directive('propositionsList', () => new propositionsListDirective(propositionsListTemplate))
    //displays the questions loop
    .directive('questionsList', () => new questionsListDirective(questionsListTemplate))
    //main directive
    .directive('simupollCreator', () => new simupollCreatorDirective('SimupollCreatorController', simupollCreatorTemplate))
    //translations
    .filter('trans', () => (string, domain = 'platform') =>
        Translator.trans(string, domain)
    )
