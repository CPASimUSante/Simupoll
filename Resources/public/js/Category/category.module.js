/**
 * CategoryApp
 */

//import classes called in dependency injection
//for $uibModal
import {} from 'angular-bootstrap'
//no need for angular & ui import
import categoryDirective from './Directives/categoryDirective'
import categoryService from './Services/CategoryService'
import categoryController from './Controllers/CategoryController'

// Category module
angular
.module('CategoryApp', [
    'ngSanitize',
    'ngRoute',
    'ui.bootstrap',
    'ui.translation'
])
.service('CategoryService', [
    '$http',
    '$q',
    categoryService
])
.factory('categoryModal', [
    '$uibModal',
    $modal => ({
      open: template => $modal.open({ template })
    })
  ])
.controller('CategoryController', [
//    'categoryModal',
    'CategoryService',
    categoryController
])
.directive('category', () => new categoryDirective('CategoryController'))
.filter(
    'unsafe',
    function ($sce) {
        return $sce.trustAsHtml;
    }
)
