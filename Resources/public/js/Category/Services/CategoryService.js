//no import of Angular stuff ($http, $q…)
export default class CategoryService {
    constructor($http, $q) {
        //declaration of variables
        this.$http          = $http
        this.$q             = $q
        this._tree          = CategoryService._getGlobal('simupollTree')
        this._sid           = CategoryService._getGlobal('simupollSid')
        this.parentTree    = []
    }

    getTree () {
      return this._tree
    }

    getSid () {
      return this._sid
    }

    /**
     * Retrieve all parent category possible for a given category
     */
    getParentCategoriesFor(category) {
        let result = {}
        const url = Routing.generate('simupoll_parent_category', {
          cid: category.id,
          sid: this._sid
        })

        //this.parentTree = result
// console.log("this.parentTree");console.log(this.parentTree);
        this.$http
          .get(url, {cid:category.id, sid:this._sid})
          .then(
            response => {
// console.log("result.id");console.log(result.id);
// console.log("response.data");console.log(response.data);
                result = response.data
                // this.parentTree = response.data
                // return this.parentTree
             },
            () => {
                console.log('Error in category list retrieving')
              }
          )
    }

    addCategory (props, category, onFail) {
      const result = { name: category.indent+'== '+props.name }
      const url = Routing.generate('simupoll_add_category', {
        sid: this._sid
      })
      //first, display new element, at the correct position
      this._tree.splice((this._tree.indexOf(category))+1, 0, result)

      //then, do the background save
      this.$http
        //pass variables to controller
        .post(url, { name: props.name, cid:category.id })
        .then(
          response => { result.id = response.data },
          //and check if it's alright
          () => {
            this._deleteCategory(result)
            onFail()
            }
        )
    }

    editCategory (originalCategory, newName, newParent, onFail) {
        //if no change, do nothing
      if (originalCategory.name === newName) {
        return
      }

      //save original value
      const originalName = originalCategory.name
      const url = Routing.generate('simupoll_edit_category', {
        id: originalCategory.id
      })

      originalCategory.name = newName

      this.$http
        .put(url, { name: newName, parentcategory: newParent })
        //if error, rollback
        .then(null, () => {
          originalCategory.name = originalName
          onFail()
        })
    }

    deleteCategory (category, onFail) {
      const url = Routing.generate('simupoll_delete_category', {
        cid: category.id
      })

      this._deleteCategory(category)

      this.$http
        .delete(url)
        .then(null, () => {
          this._tree.push(category)
          onFail()
        })
    }

    _deleteCategory (category) {
      this._tree.splice(this._tree.indexOf(category), 1)
    }

    //defined in template script
    static _getGlobal (name) {
      if (typeof window[name] === 'undefined') {
        throw new Error(
          `Expected ${name} to be exposed in a window.${name} variable`
        )
      }
      return window[name]
    }
}
