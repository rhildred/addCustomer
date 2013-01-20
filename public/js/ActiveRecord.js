
/* Simple JavaScript Inheritance
 * By John Resig http://ejohn.org/
 * MIT Licensed.
 */
// Inspired by base2 and Prototype
(function(){
  var initializing = false, fnTest = /xyz/.test(function(){xyz;}) ? /\b_super\b/ : /.*/;

  // The base Class implementation (does nothing)
  this.Class = function(){};
 
  // Create a new Class that inherits from this class
  Class.extend = function(prop) {
    var _super = this.prototype;
   
    // Instantiate a base class (but only create the instance,
    // don't run the init constructor)
    initializing = true;
    var prototype = new this();
    initializing = false;
   
    // Copy the properties over onto the new prototype
    for (var name in prop) {
      // Check if we're overwriting an existing function
      prototype[name] = typeof prop[name] == "function" &&
        typeof _super[name] == "function" && fnTest.test(prop[name]) ?
        (function(name, fn){
          return function() {
            var tmp = this._super;
           
            // Add a new ._super() method that is the same method
            // but on the super-class
            this._super = _super[name];
           
            // The method only need to be bound temporarily, so we
            // remove it when we're done executing
            var ret = fn.apply(this, arguments);       
            this._super = tmp;
           
            return ret;
          };
        })(name, prop[name]) :
        prop[name];
    }
   
    // The dummy class constructor
    function Class() {
      // All construction is actually done in the init method
      if ( !initializing && this.init )
        this.init.apply(this, arguments);
    }
   
    // Populate our constructed prototype object
    Class.prototype = prototype;
   
    // Enforce the constructor to be what we expect
    Class.prototype.constructor = Class;

    // And make this class extendable
    Class.extend = arguments.callee;
   
    return Class;
  };
})();

var ActiveRecord = Class.extend({
	implementFind: function(sMethod,sWhere, aBindVars, fCallBack){
		$.getJSON("db", {
			action : sMethod,
			object : this.name
		}, fCallBack);
	},
	find: function(sWhere, aBindVars, fCallBack){
		var that = this;
		this.implementFind('find', sWhere, aBindVars, function(data){
			var aRc = new Array();
			$.each(data.items, function(i, item) {
				item.name = that.name;
				aRc.push((new (ActiveRecord.extend(item))));
			});
			fCallBack.call(that, aRc);
		});
	},
	load: function(sWhere, aBindVars, fCallBack){
		var that = this;
		this.implementFind('load', sWhere, aBindVars, function(data){
			$.each(data, function(key, value){ that[key] = value});
			fCallBack.call(that);
		});
	},
	implementSaveDelete: function(sMethod, fCallBack){
		var that = this;
		var oParams = new Object(); 
		oParams.action = sMethod;
		oParams.object = this.name;
		$.each(this, function(key, value){
			if(that.hasOwnProperty(key)){
				oParams[key] = value;
			}
		});
		$.getJSON("db", oParams, function(data){
			$.each(data, function(key, value){ 
				that[key] = value;
			});
			fCallBack.call(that);
		});
	},
	save: function(fCallBack){
		this.implementSaveDelete('save', fCallBack);
	},
	'delete': function(fCallBack){
		this.implementSaveDelete('delete', fCallBack);		
	}
});
