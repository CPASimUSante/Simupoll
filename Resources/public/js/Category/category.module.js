/**
 * CategoryApp
 */

import angular from 'angular/index'
//import classes called in dependency injection
//for $uibModal
import {} from 'angular-bootstrap'

import categoryTemplate from './Partials/category.directive.html'
import categoryDirective from './Directives/categoryDirective'
import categoryService from './Services/CategoryService'
import categoryController from './Controllers/CategoryController'

// Category module
angular
    .module('CategoryApp', [
        'ui.bootstrap'
    ])
    .service('CategoryService', [
        '$http',
        '$q',
        categoryService
    ])
    //modal management
    .factory('categoryModal', [
        '$uibModal',
        $modal => ({
            open: template => $modal.open({ template })
        })
      ])
    .controller('CategoryController', [
        'CategoryService',
        'categoryModal',
        categoryController
    ])
    .directive('category', () => new categoryDirective('CategoryController', categoryTemplate))
    .filter(
        'unsafe',
        function ($sce) {
            return $sce.trustAsHtml;
        }
    )
    //translations
    .filter('trans', () => (string, domain = 'platform') =>
        Translator.trans(string, domain)
    )
