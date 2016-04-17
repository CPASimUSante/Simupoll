
/**
 * CategoryApp
 */
//import classes called in dependency injection
//no need for angular / ui import
import category from './Directives/category.directive'

const dependencies = [
    'ngSanitize',
    'ngRoute',
    'angular-loading-bar',
    'ui.bootstrap',
    'ui.translation'
];
// category module
angular
.module('CategoryApp', dependencies)
.config([
    'cfpLoadingBarProvider',
    function CategoryAppConfig(cfpLoadingBarProvider) {
        // please wait spinner config
        cfpLoadingBarProvider.latencyThreshold = 200;
        cfpLoadingBarProvider.includeBar = false;
        cfpLoadingBarProvider.spinnerTemplate = '<div class="loading">Loading&#8230;</div>';
    }
])
//Import of directive & main service
.directive('category', () => new category)
.service('CategoryService', () => new CategoryService)
.filter(
    'unsafe',
    function ($sce) {
        return $sce.trustAsHtml;
    }
);
