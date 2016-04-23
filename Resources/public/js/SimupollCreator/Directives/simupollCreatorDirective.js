export default class simupollCreatorDirective {
    constructor(SimupollCreatorController) {
        //class => use this
        this.restrict       ='E'
        this.replace        = true
        this.controller     = SimupollCreatorController
        this.controllerAs   = 'SimupollCreatorController'
        this.templateUrl    = AngularApp.webDir + 'bundles/cpasimusantesimupoll/js/SimupollCreator/Partials/simupollCreator.directive.html'
    }

    link(scope, element, attr, PeriodController) {

    }
}
