export default class CategoryCtrl {
//no import of Angular stuff ($window, $scope…)
    constructor($window, $scope, CategoryService) {
        //declaration of variables
        this.$window = $window;
        this.$scope = $scope;
        this.CategoryService = CategoryService;
    }

    // init(paper, exercise, user, currentStepIndex) {
    //     this.exercise = this.PlayerDataSharing.setExercise(exercise);
    // };
}
