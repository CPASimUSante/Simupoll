/**
 * SimupollCreatorApp
 */

import angular from 'angular/index'
//import classes called in dependency injection
//for $uibModal
import {} from 'angular-bootstrap'
import {} from 'angular-ui-tinymce'

import questionsListDirective from './Directives/questionsListDirective'
import propositionsListDirective from './Directives/propositionsListDirective'
import simupollCreatorDirective from './Directives/simupollCreatorDirective'
import simupollCreatorController from './Controllers/SimupollCreatorController'

//import classes called in dependency injection
angular
    .module('SimupollCreatorApp', [
        'ui.bootstrap',
        'ui.tinymce'
    ])
    //modal management
    .factory('simupollCreatorModal', [
        '$uibModal',
        $modal => ({
            open: template => $modal.open({ template })
        })
      ])
    .controller('SimupollCreatorController', [
        'simupollCreatorModal',
        simupollCreatorController
    ])
    //displays the propositions loop
    .directive('propositionsList', () => new propositionsListDirective)
    //displays the questions loop
    .directive('questionsList', () => new questionsListDirective)
    //main directive
    .directive('simupollCreator', () => new simupollCreatorDirective('SimupollCreatorController'))
    //translations
    .filter('trans', () => (string, domain = 'platform') =>
        Translator.trans(string, domain)
    )
