/*! Fabrik */
var FbSlider=new Class({Extends:FbElement,initialize:function(a,b){this.setPlugin("slider"),this.parent(a,b),this.makeSlider()},makeSlider:function(){var a=!1;("null"===typeOf(this.options.value)||""===this.options.value)&&(this.options.value="",a=!0),this.options.value=""===this.options.value?"":this.options.value.toInt();var b=this.options.value;if(this.options.editable===!0){if("null"===typeOf(this.element))return void fconsole("no element found for slider");this.output=this.element.getElement(".fabrikinput"),this.output2=this.element.getElement(".slider_output"),this.output.value=this.options.value,this.output2.set("text",this.options.value),this.mySlide=new Slider(this.element.getElement(".fabrikslider-line"),this.element.getElement(".knob"),{onChange:function(a){this.output.value=a,this.options.value=a,this.output2.set("text",a),this.output.fireEvent("blur",new Event.Mock(this.output,"blur")),this.callChange()}.bind(this),onComplete:function(){this.output.fireEvent("blur",new Event.Mock(this.output,"change")),this.element.fireEvent("change",new Event.Mock(this.element,"change"))}.bind(this),steps:this.options.steps}).set(b),a&&(this.output.value="",this.output2.set("text",""),this.options.value=""),this.watchClear()}},watchClear:function(){this.element.addEvent("click:relay(.clearslider)",function(a){a.preventDefault(),this.mySlide.set(0),this.output.value="",this.output.fireEvent("blur",new Event.Mock(this.output,"change")),this.output2.set("text","")}.bind(this))},getValue:function(){return this.options.value},callChange:function(){"function"===typeOf(this.changejs)?this.changejs.delay(0):eval(this.changejs)},addNewEvent:function(a,b){return"load"===a?(this.loadEvents.push(b),void this.runLoadEvent(b)):void("change"===a&&(this.changejs=b))},cloned:function(a){delete this.mySlide,this.makeSlider(),this.parent(a)}});