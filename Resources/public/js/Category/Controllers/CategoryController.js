//the template for modals
import addCategoryTemplate from '../Partials/modalAddCategory.html'
import editCategoryTemplate from '../Partials/modalEditCategory.html'
import deleteCategoryTemplate from '../Partials/modalDeleteCategory.html'

export default class CategoryController {
//no import of Angular stuff ($window, $scope…)
//    constructor($window, $scope, categoryModal, CategoryService) {
    constructor(CategoryService) {
        //declaration of variables
        this.CategoryService    = CategoryService
        this.tree               = ''
        this.sid                = 0
        this.addedCategory      = {}
        this.editedCategory     = {}
        //this._modalFactory      = categoryModal
        this._modalInstance     = null
    }

    init(tree, sid) {
        this.tree   = JSON.parse(tree)
        this.sid    = sid
    }

    showAddCategoryForm (category) {
      this._modal(addCategoryTemplate)
    }

    showEditCategoryForm (category) {
    //   this.editedCategory.original = category
    //   this.editedCategory.newValue = category.name
      this._modal(editCategoryTemplate)
    }

    editCategory(sid) {
        console.log('edited');
    }

    addCategory(sid) {
        console.log('add');
    }

    deleteCategory(sid) {
        console.log('deleted');
    }

    _modal (template, errorMessage, errors) {
      if (errorMessage) {
        this.errorMessage = errorMessage
      }
      if (errors) {
        this.errors = errors
      }
      this._modalInstance = this._modalFactory.open(template)
    }

    _closeModal () {
      this._modalInstance.close()
    }
}
