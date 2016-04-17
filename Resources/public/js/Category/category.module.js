/**
 * CategoryApp
 */

(function () {
    'use strict';

    var dependencies = [
        'ngSanitize',
        'ngRoute',
        'angular-loading-bar',
        'ui.bootstrap',
        'ui.translation',
        'Common'
    ];
    // category module
    var CategoryApp = angular.module('CategoryApp', dependencies);

    CategoryApp.config([
        'cfpLoadingBarProvider',
        function CategoryAppConfig(cfpLoadingBarProvider) {
            // please wait spinner config
            cfpLoadingBarProvider.latencyThreshold = 200;
            cfpLoadingBarProvider.includeBar = false;
            cfpLoadingBarProvider.spinnerTemplate = '<div class="loading">Loading&#8230;</div>';
        }
    ]);

     CategoryApp.filter(
        'unsafe',
        function ($sce) {
            return $sce.trustAsHtml;
        });
})();
