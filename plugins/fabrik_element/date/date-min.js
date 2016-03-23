/*! Fabrik */
var FbDateTime=new Class({Extends:FbElement,options:{dateTimeFormat:"",calendarSetup:{eventName:"click",ifFormat:"%Y/%m/%d",daFormat:"%Y/%m/%d",singleClick:!0,align:"Tl",range:[1900,2999],showsTime:!1,timeFormat:"24",electric:!0,step:2,cache:!1,showOthers:!1,advanced:!1,allowedDates:[]}},initialize:function(a,b){return this.setPlugin("fabrikdate"),this.parent(a,b)?(this.hour="0",this.minute="00",this.buttonBg="#ffffff",this.buttonBgSelected="#88dd33",this.startElement=a,this.setUpDone=!1,this.convertAllowedDates(),void this.setUp()):!1},convertAllowedDates:function(){for(var a=0;a<this.options.allowedDates.length;a++)this.options.allowedDates[a]=new Date(this.options.allowedDates[a])},setUp:function(){if(this.options.editable){this.watchButtons(),this.options.typing===!1?this.disableTyping():this.getDateField().addEvent("blur",function(){var a=this.getDateField().value;if(""!==a){var b;b=this.options.advanced?Date.parseExact(a,Date.normalizeFormat(this.options.calendarSetup.ifFormat)):Date.parseDate(a,this.options.calendarSetup.ifFormat),this.setTimeFromField(b),this.update(b)}else this.options.value=""}.bind(this)),this.makeCalendar();var a=function(){this.cal.hide()};a.delay(100,this),this.getCalendarImg().addEvent("click",function(a){a.stop(),this.cal.params.position?this.cal.showAt(this.cal.params.position[0],params.position[1]):this.cal.showAtElement(this.cal.params.button||this.cal.params.displayArea||this.cal.params.inputField,this.cal.params.align),this.cal._init(this.cal.firstDayOfWeek,this.cal.date),this.cal.show()}.bind(this)),Fabrik.addEvent("fabrik.form.submit.failed",function(){this.afterAjaxValidation()}.bind(this))}},attachedToForm:function(){this.parent(),this.watchAjaxTrigger()},watchAjaxTrigger:function(){if(""!==this.options.watchElement){var a=this.form.elements[this.options.watchElement];a&&a.addEvent("change",function(){var b={option:"com_fabrik",format:"raw",task:"plugin.pluginAjax",plugin:"date",method:"ajax_getAllowedDates",element_id:this.options.id,v:a.get("value"),formid:this.form.id};new Request.JSON({url:"",method:"post",data:b,onSuccess:function(a){this.options.allowedDates=a,this.convertAllowedDates()}.bind(this)}).send()}.bind(this))}},getCalendarImg:function(){var a=this.element.getElement(".calendarbutton");return a},dateSelect:function(date){var allowed=this.options.allowedDates;if(allowed.length>0){for(var matched=!1,i=0;i<allowed.length;i++)allowed[i].format("%Y%m%d")===date.format("%Y%m%d")&&(matched=!0);if(!matched)return!0}var fn=this.options.calendarSetup.dateAllowFunc;return"null"!==typeOf(fn)&&""!==fn?(eval(fn),result):void 0},calSelect:function(a){if(a.dateClicked&&!this.dateSelect(a.date)){var b=this.setTimeFromField(a.date);this.update(b.format("db")),this.getDateField().fireEvent("change"),this.timeButton&&this.getTimeField().fireEvent("change"),this.cal.callCloseHandler(),window.fireEvent("fabrik.date.select",this),Fabrik.fireEvent("fabrik.date.select",this)}},calClose:function(){this.cal.hide(),window.fireEvent("fabrik.date.close",this),this.options.validations&&this.form.doElementValidation(this.options.element)},onsubmit:function(a){var b=this.getValue();""!==b&&this.options.editable&&(this.getDateField().value=b),this.parent(a)},afterAjaxValidation:function(){this.update(this.getValue(),[])},makeCalendar:function(){if(this.cal)return void this.cal.show();this.addEventToCalOpts();var a=this.options.calendarSetup,b=["displayArea","button"];for(i=0;i<b.length;i++)"string"==typeof a[b[i]]&&(a[b[i]]=document.getElementById(a[b[i]]));a.inputField=this.getDateField();var c=a.inputField||a.displayArea,d=a.inputField?a.ifFormat:a.daFormat;if(this.cal=null,c&&(this.options.advanced?""===c.value?a.date="":(a.date=Date.parseExact(c.value||c.innerHTML,Date.normalizeFormat(d)),null===a.date&&(a.date=this.options.value)):a.date=Date.parseDate(c.value||c.innerHTML,d)),this.cal=new Calendar(a.firstDay,a.date,a.onSelect,a.onClose),this.cal.setDateStatusHandler(a.dateStatusFunc),this.cal.setDateToolTipHandler(a.dateTooltipFunc),this.cal.showsTime=a.showsTime,this.cal.time24="24"===a.timeFormat.toString(),this.cal.weekNumbers=a.weekNumbers,a.multiple)for(cal.multiple={},i=a.multiple.length;--i>=0;){var e=a.multiple[i],f=e.print("%Y%m%d");this.cal.multiple[f]=e}this.cal.showsOtherMonths=a.showOthers,this.cal.yearStep=a.step,this.cal.setRange(a.range[0],a.range[1]),this.cal.params=a,this.cal.getDateText=a.dateText,this.cal.setDateFormat(d),this.cal.create(),this.cal.refresh(),this.cal.hide()},disableTyping:function(){return"null"===typeOf(this.element)?void fconsole(element+": not date element container - is this a custom template with a missing $element->containerClass div/li surrounding the element?"):(this.element.setProperty("readonly","readonly"),void this.element.getElements(".fabrikinput").each(function(a){a.addEvent("focus",function(b){this._disabledShowCalTime(a,b)}.bind(this)),a.addEvent("click",function(b){this._disabledShowCalTime(a,b)}.bind(this))}.bind(this)))},_disabledShowCalTime:function(a,b){"null"!==typeOf(b)&&(b.target.hasClass("timeField")?this.getContainer().getElement(".timeButton").fireEvent("click"):(this.options.calendarSetup.inputField=b.target.id,this.options.calendarSetup.button=this.element.id+"_img",this.cal.showAtElement(a,this.cal.params.align),"undefined"!=typeof this.cal.wrapper&&this.cal.wrapper.getParent().position({relativeTo:this.cal.params.inputField,position:"topLeft"})))},getValue:function(){var a;if(!this.options.editable)return this.options.value;if(this.getElement(),this.cal){var b=this.getDateField().value;if(""===b)return"";var c=new RegExp("\\d{4}-\\d{2}-\\d{2} \\d{2}:\\d{2}:\\d{2}");if(null!==b.match(c))return b;a=this.cal.date}else{if(""===this.options.value||null===this.options.value||"0000-00-00 00:00:00"===this.options.value)return"";a=new Date.parse(this.options.value)}return a=this.setTimeFromField(a),a.format("db")},hasSeconds:function(){if(this.options.showtime===!0&&this.timeElement){if(this.options.dateTimeFormat.contains("%S"))return!0;if(this.options.dateTimeFormat.contains("%T"))return!0;if(this.options.dateTimeFormat.contains("s"))return!0}return!1},setTimeFromField:function(a){if("date"===typeOf(a)){if(this.options.showtime===!0&&this.timeElement){var b=this.timeElement.get("value").toUpperCase(),c=b.contains("PM")?!0:!1;b=b.replace("PM","").replace("AM","");var d=b.split(":"),e=d[0]?d[0].toInt():0;c&&(e+=12);var f=d[1]?d[1].toInt():0;if(a.setHours(e),a.setMinutes(f),d[2]&&this.hasSeconds()){var g=d[2]?d[2].toInt():0;a.setSeconds(g)}else a.setSeconds(0)}return a}},watchButtons:function(){this.options.showtime&this.options.editable&&(this.getTimeField(),this.getTimeButton(),this.timeButton&&(this.timeButton.removeEvents("click"),this.timeButton.addEvent("click",function(a){a.stop(),this.showTime()}.bind(this)),this.setUpDone||this.timeElement&&(this.dropdown=this.makeDropDown(),this.setActive(),this.dropdown.getElement("a.close-time").addEvent("click",function(a){a.stop(),this.hideTime()}.bind(this)),this.setUpDone=!0)))},addNewEventAux:function(action,js){"change"===action?Fabrik.addEvent("fabrik.date.select",function(w){if(w.baseElementId===this.baseElementId){var e="fabrik.date.select";"function"===typeOf(js)?js.delay(0,this,this):eval(js)}}.bind(this)):this.element.getElements("input").each(function(i){i.addEvent(action,function(e){"event"===typeOf(e)&&e.stop(),"function"===typeOf(js)?js.delay(0,this,this):eval(js)})}.bind(this))},update:function(a,b){if(b=b?b:["change"],this.getElement(),"invalid date"===a)return void fconsole(this.element.id+": date not updated as not valid");var c;if("string"===typeOf(a)){if(c=Date.parse(a),null===c)return this._getSubElements().each(function(a){a.value=""}),this.cal&&(this.cal.date=new Date),void(this.options.editable||"null"!==typeOf(this.element)&&this.element.set("html",a))}else c=a;var d=this.options.calendarSetup.ifFormat;if(""!==this.options.dateTimeFormat&&this.options.showtime&&(d+=" "+this.options.dateTimeFormat),b.length>0&&this.fireEvents(b),"null"!==typeOf(a)&&a!==!1){if(!this.options.editable)return void("null"!==typeOf(this.element)&&this.element.set("html",c.format(d)));if(this.options.hidden)return c=c.format(d),void(this.getDateField().value=c);this.getTimeField(),this.hour=c.get("hours"),this.minute=c.get("minutes"),this.second=c.get("seconds"),this.stateTime(),this.cal.date=c,this.getDateField().value=c.format(this.options.calendarSetup.ifFormat)}},getDateField:function(){return this.element.getElement(".fabrikinput")},getTimeField:function(){return this.timeElement=this.getContainer().getElement(".timeField"),this.timeElement},getTimeButton:function(){return this.timeButton=this.getContainer().getElement(".timeButton"),this.timeButton},showCalendar:function(){},getAbsolutePos:function(a){var b={x:a.offsetLeft,y:a.offsetTop};if(a.offsetParent){var c=this.getAbsolutePos(a.offsetParent);b.x+=c.x,b.y+=c.y}return b},makeDropDown:function(){var a=null,b=new Element("div.draggable.modal-header",{styles:{height:"20px",curor:"move",padding:"2px;"},id:this.startElement+"_handle"}).set("html",'<i class="icon-clock"></i> '+this.options.timelabel+'<a href="#" class="close-time pull-right" ><i class="icon-cancel"></i></a>'),c=new Element("div.fbDateTime.fabrikWindow",{styles:{"z-index":999999,display:"none",width:"300px",height:"180px"}});c.appendChild(b);var d=new Element("div.itemContentPadder");d.adopt(new Element("p").set("text","Hours")),d.adopt(this.hourButtons(0,12)),d.adopt(this.hourButtons(12,24)),d.adopt(new Element("p").set("text","Minutes"));var e=new Element("div.btn-group",{styles:{clear:"both",paddingTop:"5px"}});for(i=0;i<12;i++)a=new Element("a.btn.fbdateTime-minute.btn-mini",{styles:{width:"10px"}}),a.innerHTML=5*i,e.appendChild(a),document.id(a).addEvent("click",function(a){this.minute=this.formatMinute(a.target.innerHTML),this.stateTime(),this.setActive()}.bind(this)),a.addEvent("mouseover",function(a){var b=a.target;this.minute!==this.formatMinute(b.innerHTML)&&a.target.addClass("btn-info")}.bind(this)),a.addEvent("mouseout",function(a){var b=a.target;this.minute!==this.formatMinute(b.innerHTML)&&a.target.removeClass("btn-info")}.bind(this));d.appendChild(e),c.appendChild(d),document.addEvent("click",function(a){if(this.timeActive){var b=a.target;b!==this.timeButton&&b!==this.timeElement&&(b.within(this.dropdown)||this.hideTime())}}.bind(this)),c.inject(document.body);var f=(new Drag.Move(c),b.getElement("a.close"));return"null"!==typeOf(f)&&f.addEvent("click",function(a){a.stop(),this.hideTime()}.bind(this)),c},hourButtons:function(a,b){var c=this.getValue();if(""===c)this.hour=0,this.minute=0;else{var d=Date.parse(c);this.hour=d.get("hours"),this.minute=d.get("minutes")}for(var e=new Element("div.btn-group"),f=a;b>f;f++)h=new Element("a.btn.btn-mini.fbdateTime-hour",{styles:{width:"10px"}}).set("html",f),e.appendChild(h),document.id(h).addEvent("click",function(a){this.hour=a.target.innerHTML,this.stateTime(),this.setActive(),a.target.addClass("btn-successs").removeClass("badge-info")}.bind(this)),document.id(h).addEvent("mouseover",function(a){this.hour!==a.target.innerHTML&&a.target.addClass("btn-info")}.bind(this)),document.id(h).addEvent("mouseout",function(a){this.hour!==a.target.innerHTML&&a.target.removeClass("btn-info")}.bind(this));return e},toggleTime:function(){"none"===this.dropdown.style.display?this.doShowTime():this.hideTime()},doShowTime:function(){this.dropdown.show(),this.timeActive=!0,Fabrik.fireEvent("fabrik.date.showtime",this)},hideTime:function(){this.timeActive=!1,this.dropdown.hide(),this.options.validations!==!1&&this.form.doElementValidation(this.element.id),Fabrik.fireEvent("fabrik.date.hidetime",this),Fabrik.fireEvent("fabrik.date.select",this),window.fireEvent("fabrik.date.select",this)},formatMinute:function(a){return a=a.replace(":",""),a.pad("2","0","left"),a},stateTime:function(){if(this.timeElement){var a=this.hour.toString().pad("2","0","left")+":"+this.minute.toString().pad("2","0","left");this.second&&(a+=":"+this.second.toString().pad("2","0","left"));var b=this.timeElement.value!==a;this.timeElement.value=a,b&&this.fireEvents(["change"])}},showTime:function(){this.dropdown.position({relativeTo:this.timeElement,position:"topRight"}),this.toggleTime(),this.setActive()},setActive:function(){var a=this.dropdown.getElements(".fbdateTime-hour");a.removeClass("btn-success").removeClass("btn-info");var b=this.dropdown.getElements(".fbdateTime-minute");b.removeClass("btn-success").removeClass("btn-info"),"null"!==typeOf(b[this.minute/5])&&b[this.minute/5].addClass("btn-success");var c=a[this.hour.toInt()];"null"!==typeOf(c)&&c.addClass("btn-success")},addEventToCalOpts:function(){this.options.calendarSetup.onSelect=function(a,b){this.calSelect(a,b)}.bind(this),this.options.calendarSetup.dateStatusFunc=function(a){return this.dateSelect(a)}.bind(this),this.options.calendarSetup.onClose=function(a){this.calClose(a)}.bind(this)},cloned:function(a){this.setUpDone=!1,this.hour=0,delete this.cal;var b=this.element.getElement("img");b&&(b.id=this.element.id+"_cal_img");var c=this.element.getElement("input");c.id=this.element.id+"_cal",this.options.calendarSetup.inputField=c.id,this.options.calendarSetup.button=this.element.id+"_img",this.makeCalendar(),this.cal.hide(),this.setUp(),this.parent(a)}});