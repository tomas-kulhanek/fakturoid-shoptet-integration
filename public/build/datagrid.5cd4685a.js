(self.webpackChunk=self.webpackChunk||[]).push([[616,143],{8144:(t,e,a)=>{"use strict";a(4812),a(3734),a(3752);var i=a(4609),n=(a(300),a(9127)),r=a.n(n);window.LiveForm=r().LiveForm,window.Nette=r().Nette,document.addEventListener("DOMContentLoaded",i.default.initialize.bind(i.default)),document.addEventListener("DOMContentLoaded",(function(){window.Nette.init(),window.LiveForm.setOptions({showMessageClassOnParent:!1,controlErrorClass:"is-invalid",controlValidClass:"is-valid",messageErrorClass:"error invalid-feedback",messageTag:"span",showValid:!0,messageErrorPrefix:'&nbsp;<i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;'})}))},2425:(t,e,a)=>{"use strict";a(8144),a(595),a(9202),a(6567)},6567:(t,e,a)=>{var i,n=a(4609).default,r=a(9755);i=void 0!==n?function(t){var e=t.type||"GET",a=t.data||null;n.makeRequest(e,t.url,a,{history:"replace"}).then(t.success).catch(t.error)}:function(t){r.nette.ajax(t)},document.addEventListener("DOMContentLoaded",(function(){var t=document.querySelector(".datagrid");if(null!==t)return i({type:"GET",url:t.getAttribute("data-refresh-state")})}))},9202:(t,e,a)=>{var i,n=a(4609).default,r=a(9755);if(void 0!==n){var d=function(){return n&&n.VERSION&&n.VERSION>=2},o=function(t){return d()?t.detail:t},l=function(t){return d()?t.detail.request:t.xhr};i=function(t,e){var a=e.init,i=e.success,r=e.before,s=e.complete,c=function(t,e){return this.name=e,this.initialize=function(t){a&&t.addEventListener("init",(function(t){a(o(t).defaultOptions)})),i&&t.addEventListener("success",(function(t){var e=d()?t.detail.payload:t.response;i(e,o(t).options)})),r&&t.addEventListener("before",(function(t){r(l(t),o(t).options)})),s&&t.addEventListener("complete",(function(t){s(l(t),o(t).options)}))},d()||this.initialize(t),this};d()?n.registerExtension(new c(null,t)):n.registerExtension(c,t)}}else r.nette&&(i=function(t,e){r.nette.ext(t,e)});i("ublaboo-spinners",{before:function(t,e){var a,i,n,d;if(e.nette){if(a=e.nette.el,n=r('<div class="ublaboo-spinner ublaboo-spinner-small"><i></i><i></i><i></i><i></i></div>'),a.is('.datagrid [name="group_action[submit]"]'))return a.after(n);if(a.is(".datagrid a")&&a.data("toggle-detail")){if(i=e.nette.el.attr("data-toggle-detail"),d=e.nette.el.attr("data-toggle-detail-grid-fullname"),!r(".item-detail-"+d+"-id-"+i).hasClass("loaded"))return a.addClass("ublaboo-spinner-icon")}else{if(a.is(".datagrid .col-pagination a"))return a.closest(".row-grid-bottom").find(".col-per-page").prepend(n);if(a.is(".datagrid .datagrid-per-page-submit"))return a.closest(".row-grid-bottom").find(".col-per-page").prepend(n);if(a.is(".datagrid .reset-filter"))return a.closest(".row-grid-bottom").find(".col-per-page").prepend(n)}}},complete:function(){return r(".ublaboo-spinner").remove(),r(".ublaboo-spinner-icon").removeClass("ublaboo-spinner-icon")}})},595:(t,e,a)=>{var i,n,r,d,o=a(4609).default,l=a(9755);if(void 0!==o){var s=function(){return o&&o.VERSION&&o.VERSION>=2},c=function(t){return s()?t.detail:t},u=function(t){return s()?t.detail.request:t.xhr};i=function(t,e){var a=e.init,i=e.success,n=e.before,r=e.complete,d=e.interaction,g=function(t,e){return this.name=e,this.initialize=function(t){a&&t.addEventListener("init",(function(t){a(c(t).defaultOptions)})),i&&t.addEventListener("success",(function(t){var e=s()?t.detail.payload:t.response;i(e,c(t).options)}));var e=t;s()&&(e=e.uiHandler),e.addEventListener("interaction",(function(t){s()?t.detail.options.nette={el:l(t.detail.element)}:t.options.nette={el:l(t.element)},d&&(d(c(t).options)||t.preventDefault())})),n&&t.addEventListener("before",(function(t){n(u(t),c(t).options)||t.preventDefault()})),r&&t.addEventListener("complete",(function(t){r(u(t),c(t).options)}))},s()||this.initialize(t),this};s()?o.registerExtension(new g(null,t)):o.registerExtension(g,t)},n=function(t){var e=t.type||"GET",a=t.data||null;o.makeRequest(e,t.url,a,{}).then(t.success).catch(t.error)},r=function(){o.load()},d=function(t){return o.uiHandler.submitForm(t.get(0))}}else{if(!l.nette)throw new Error("Include Naja.js or nette.ajax for datagrids to work!");i=function(t,e){l.nette.ext(t,e)},n=function(t){l.nette.ajax(t)},r=function(){l.nette.load()},d=function(t){return t.submit()}}var g,f,h,p,m,v=[].indexOf||function(t){for(var e=0,a=this.length;e<a;e++)if(e in this&&this[e]===t)return e;return-1};l(document).on("click","[data-datagrid-confirm]:not(.ajax)",(function(t){if(!confirm(l(t.target).closest("a").attr("data-datagrid-confirm")))return t.stopPropagation(),t.preventDefault()})),i("datagrid.confirm",void 0!==o?{interaction:function(t){var e;return!t.nette||!(e=t.nette.el.data("datagrid-confirm"))||confirm(e)}}:{before:function(t,e){var a;return!e.nette||!(a=e.nette.el.data("datagrid-confirm"))||confirm(a)}}),l(document).on("change","select[data-autosubmit-per-page]",(function(){var t;return 0===(t=l(this).parent().find("input[type=submit]")).length&&(t=l(this).parent().find("button[type=submit]")),t.click()})).on("change","select[data-autosubmit]",(function(){return d(l(this).closest("form").first())})).on("change","input[data-autosubmit][data-autosubmit-change]",(function(t){var e;return t.which||t.keyCode||0,clearTimeout(window.datagrid_autosubmit_timer),e=l(this),window.datagrid_autosubmit_timer=setTimeout((function(){return d(e.closest("form").first())}),200)})).on("keyup","input[data-autosubmit]",(function(t){var e,a;if(13===(a=t.which||t.keyCode||0)||!(a>=9&&a<=40||a>=112&&a<=123))return clearTimeout(window.datagrid_autosubmit_timer),e=l(this),window.datagrid_autosubmit_timer=setTimeout((function(){return d(e.closest("form").first())}),200)})).on("keydown",".datagrid-inline-edit input",(function(t){if(13===(t.which||t.keyCode||0))return t.stopPropagation(),t.preventDefault(),l(this).closest("tr").find('.col-action-inline-edit [name="inline_edit[submit]"]').click()})),l(document).on("keydown","input[data-datagrid-manualsubmit]",(function(t){if(13===(t.which||t.keyCode||0))return t.stopPropagation(),t.preventDefault(),d(l(this).closest("form").first())})),m=function(t){var e,a;if(v.call(t,a)>=0)return t.path;for(a=[],e=t.target;e!==document.body&&null!==e;)a.push(e),e=e.parentNode;return a},function(){var t;t=null,document.addEventListener("click",(function(e){var a,i,n,r,d,o,s,c,u,g,f,h,p,v,b,_,w;for(d=0,g=(p=m(e)).length;d<g;d++)if(n=p[d],l(n).is(".col-checkbox")&&t&&e.shiftKey){if(i=l(n).closest("tr"),a=(u=t.closest("tr")).closest("tbody").find("tr").toArray(),i.index()>u.index()?w=a.slice(u.index(),i.index()):i.index()<u.index()&&(w=a.slice(i.index()+1,u.index())),!w)return;for(s=0,f=w.length;s<f;s++)_=w[s],(o=l(_).find(".col-checkbox input[type=checkbox]")[0])&&(o.checked=!0,window.navigator.userAgent.indexOf("MSIE ")?(r=document.createEvent("Event")).initEvent("change",!0,!0):r=new Event("change",{bubbles:!0}),o.dispatchEvent(r))}for(b=[],c=0,h=(v=m(e)).length;c<h;c++)n=v[c],l(n).is(".col-checkbox")?b.push(t=l(n)):b.push(void 0);return b}))}(),document.addEventListener("change",(function(t){var e,a,i,n,r,d,o,l,s,c,u,g;if((r=t.target.getAttribute("data-check"))&&(a=document.querySelectorAll("input[data-check-all-"+r+"]:checked"),u=document.querySelector(".datagrid-"+r+' select[name="group_action[group_action]"]'),0===(e=document.querySelectorAll(".datagrid-"+r+' input[type="submit"]')).length&&(e=document.querySelectorAll(".datagrid-"+r+' button[type="submit"]')),i=document.querySelector(".datagrid-"+r+" .datagrid-selected-rows-count"),a.length?(e&&e.forEach((function(t){t.disabled=!1})),u&&(u.disabled=!1),g=document.querySelectorAll("input[data-check-all-"+r+"]").length,i&&(i.innerHTML=a.length+"/"+g)):(e&&e.forEach((function(t){t.disabled=!0})),u&&(u.disabled=!0,u.value=""),i&&(i.innerHTML="")),window.navigator.userAgent.indexOf("MSIE ")?(n=document.createEvent("Event")).initEvent("change",!0,!0):n=new Event("change",{bubbles:!0}),u&&u.dispatchEvent(n)),r=t.target.getAttribute("data-check-all")){for(c=[],d=0,s=(l=document.querySelectorAll("input[type=checkbox][data-check-all-"+r+"]")).length;d<s;d++)(o=l[d]).checked=t.target.checked,window.navigator.userAgent.indexOf("MSIE ")?(n=document.createEvent("Event")).initEvent("change",!0,!0):n=new Event("change",{bubbles:!0}),c.push(o.dispatchEvent(n));return c}})),window.datagridSerializeUrl=function(t,e){var a=[];for(var i in t)if(t.hasOwnProperty(i)){var n=e?e+"["+i+"]":i,r=t[i];if(null!==r&&""!==r)if("object"==typeof r){var d=window.datagridSerializeUrl(r,n);d&&a.push(d)}else a.push(encodeURIComponent(n)+"="+encodeURIComponent(r))}return a.join("&")},h=function(){if(void 0!==l.fn.sortable)return l(".datagrid [data-sortable]").sortable({handle:".handle-sort",items:"tr",axis:"y",update:function(t,e){var a,i,r,d,o,s,c;return r=(s=e.item.closest("tr[data-id]")).data("id"),o=null,d=null,s.prev().length&&(o=s.prev().data("id")),s.next().length&&(d=s.next().data("id")),c=l(this).data("sortable-url"),(i={})[((a=s.closest(".datagrid").find("tbody").attr("data-sortable-parent-path"))+"-item_id").replace(/^-/,"")]=r,null!==o&&(i[(a+"-prev_id").replace(/^-/,"")]=o),null!==d&&(i[(a+"-next_id").replace(/^-/,"")]=d),n({type:"GET",url:c,data:i,error:function(t,e,a){return alert(t.statusText)}})},helper:function(t,e){return e.children().each((function(){return l(this).width(l(this).width())})),e}})},l((function(){return h()})),void 0===p&&(p=function(){if(void 0!==l(".datagrid-tree-item-children").sortable)return l(".datagrid-tree-item-children").sortable({handle:".handle-sort",items:".datagrid-tree-item:not(.datagrid-tree-header)",toleranceElement:"> .datagrid-tree-item-content",connectWith:".datagrid-tree-item-children",update:function(t,e){var a,i,r,d,o,s,c,u,g;if(l(".toggle-tree-to-delete").remove(),r=(u=e.item.closest(".datagrid-tree-item[data-id]")).data("id"),c=null,d=null,s=null,u.prev().length&&(c=u.prev().data("id")),u.next().length&&(d=u.next().data("id")),(o=u.parent().closest(".datagrid-tree-item")).length&&(o.find(".datagrid-tree-item-children").first().css({display:"block"}),o.addClass("has-children"),s=o.data("id")),g=l(this).data("sortable-url"))return o.find("[data-toggle-tree]").first().removeClass("hidden"),(i={})[((a=u.closest(".datagrid-tree").attr("data-sortable-parent-path"))+"-item_id").replace(/^-/,"")]=r,null!==c&&(i[(a+"-prev_id").replace(/^-/,"")]=c),null!==d&&(i[(a+"-next_id").replace(/^-/,"")]=d),i[(a+"-parent_id").replace(/^-/,"")]=s,n({type:"GET",url:g,data:i,error:function(t,e,a){if("abort"!==a)return alert(t.statusText)}})},stop:function(t,e){return l(".toggle-tree-to-delete").removeClass("toggle-tree-to-delete")},start:function(t,e){var a;if((a=e.item.parent().closest(".datagrid-tree-item")).length&&2===a.find(".datagrid-tree-item").length)return a.find("[data-toggle-tree]").addClass("toggle-tree-to-delete")}})}),l((function(){return p()})),i("datagrid.happy",{success:function(){var t,e,a,i,n,r,d,o,s,c,u;for(window.happy&&window.happy.reset(),u=[],r=0,s=(n=l(".datagrid")).length;r<s;r++){for(e="",o=0,c=(a=n[r].classList).length;o<c;o++)e=e+"."+a[o];1===(t=document.querySelectorAll(e+" input[data-check]:checked")).length&&"toggle-all"===t[0].getAttribute("name")&&(d=document.querySelector(e+" input[name=toggle-all]"))?(d.checked=!1,window.navigator.userAgent.indexOf("MSIE ")?(i=document.createEvent("Event")).initEvent("change",!0,!0):i=new Event("change",{bubbles:!0}),u.push(d.dispatchEvent(i))):u.push(void 0)}return u}}),i("datagrid.sortable",{success:function(){return h()}}),i("datagrid.forms",{success:function(){return l(".datagrid").find("form").each((function(){return window.Nette.initForm(this)}))}}),i("datagrid.url",{success:function(t){var e,a,i,n;if(t._datagrid_url&&window.history.replaceState&&(e=window.location.protocol+"//"+window.location.host,a=window.location.pathname,n=(i=window.datagridSerializeUrl(t.state).replace(/&+$/gm,""))?e+a+"?"+i.replace(/\&*$/,""):e+a,n+=window.location.hash,window.location.href!==n))return window.history.replaceState({path:n},"",n)}}),i("datagrid.sort",{success:function(t){var e,a,i,n;if(t._datagrid_sort){for(a in n=[],i=t._datagrid_sort)e=i[a],n.push(l("#datagrid-sort-"+a).attr("href",e));return n}}}),i("datargid.item_detail",{before:function(t,e){var a,i,n;return!e.nette||!e.nette.el.attr("data-toggle-detail")||(a=e.nette.el.attr("data-toggle-detail"),n=e.nette.el.attr("data-toggle-detail-grid-fullname"),(i=l(".item-detail-"+n+"-id-"+a)).hasClass("loaded")?i.find(".item-detail-content").length?(i.hasClass("toggled")?i.find(".item-detail-content").slideToggle("fast",(function(){return i.toggleClass("toggled")})):(i.toggleClass("toggled"),i.find(".item-detail-content").slideToggle("fast")),!1):(i.removeClass("toggled"),!0):i.addClass("loaded"))},success:function(t){var e,a,i;if(t._datagrid_toggle_detail&&t._datagrid_name)return e=t._datagrid_toggle_detail,i=t._datagrid_name,(a=l(".item-detail-"+i+"-id-"+e)).toggleClass("toggled"),a.find(".item-detail-content").slideToggle("fast")}}),i("datagrid.tree",{before:function(t,e){var a;return!(e.nette&&e.nette.el.attr("data-toggle-tree")&&(e.nette.el.toggleClass("toggle-rotate"),(a=e.nette.el.closest(".datagrid-tree-item").find(".datagrid-tree-item-children").first()).hasClass("loaded")))||(a.slideToggle("fast"),!1)},success:function(t){var e,a,i,n,d,o,s;if(t._datagrid_tree){for(n in i=t._datagrid_tree,(e=l('.datagrid-tree-item[data-id="'+i+'"]').find(".datagrid-tree-item-children").first()).addClass("loaded"),d=t.snippets)o=d[n],a=l(o),(s=l('<div class="datagrid-tree-item" id="'+n+'">')).attr("data-id",a.attr("data-id")),s.append(a),a.data("has-children")&&s.addClass("has-children"),e.append(s);e.addClass("loaded"),e.slideToggle("fast"),r()}return p()}}),l(document).on("click","[data-datagrid-editable-url]",(function(t){var e,a,i,r,d,o,s,c,u,g;if(r=l(this),"a"!==t.target.tagName.toLowerCase()&&!r.hasClass("datagrid-inline-edit")&&!r.hasClass("editing")){for(e in r.addClass("editing"),d=r.html().trim().replace("<br>","\n"),g=r.attr("data-datagrid-editable-value")?r.data("datagrid-editable-value"):d,r.data("originalValue",d),r.data("valueToEdit",g),"textarea"===r.data("datagrid-editable-type")?(c=l("<textarea>"+g+"</textarea>"),s=parseInt(r.css("padding").replace(/[^-\d\.]/g,""),10),o=(r.outerHeight()-2*s)/Math.round(parseFloat(r.css("line-height"))),c.attr("rows",Math.round(o))):"select"===r.data("datagrid-editable-type")?(c=l(r.data("datagrid-editable-element"))).find("option[value='"+g+"']").prop("selected",!0):(c=l('<input type="'+r.data("datagrid-editable-type")+'">')).val(g),i=r.data("datagrid-editable-attrs"))a=i[e],c.attr(e,a);return r.removeClass("edited"),r.html(c),u=function(t,e){var a;return(a=e.val())!==t.data("valueToEdit")?n({url:t.data("datagrid-editable-url"),data:{value:a},type:"POST",success:function(e){return"select"===t.data("datagrid-editable-type")?t.html(c.find("option[value='"+a+"']").html()):(e._datagrid_editable_new_value&&(a=e._datagrid_editable_new_value),t.html(a)),t.addClass("edited")},error:function(){return t.html(t.data("originalValue")),t.addClass("edited-error")}}):t.html(t.data("originalValue")),setTimeout((function(){return t.removeClass("editing")}),1200)},r.find("input,textarea,select").focus().on("blur",(function(){return u(r,l(this))})).on("keydown",(function(t){return"textarea"!==r.data("datagrid-editable-type")&&13===t.which?(t.stopPropagation(),t.preventDefault(),u(r,l(this))):27===t.which?(t.stopPropagation(),t.preventDefault(),r.removeClass("editing"),r.html(r.data("originalValue"))):void 0})),r.find("select").on("change",(function(){return u(r,l(this))}))}})),i("datagrid.after_inline_edit",{success:function(t){var e=l(".datagrid-"+t._datagrid_name);return t._datagrid_inline_edited?(e.find("tr[data-id="+t._datagrid_inline_edited+"] > td").addClass("edited"),e.find(".datagrid-inline-edit-trigger").removeClass("hidden")):t._datagrid_inline_edit_cancel?e.find(".datagrid-inline-edit-trigger").removeClass("hidden"):void 0}}),l(document).on("mouseup","[data-datagrid-cancel-inline-add]",(function(t){if(1===(t.which||t.keyCode||0))return t.stopPropagation(),t.preventDefault(),l(".datagrid-row-inline-add").addClass("datagrid-row-inline-add-hidden")})),i("datagrid-toggle-inline-add",{success:function(t){var e=l(".datagrid-"+t._datagrid_name);if(t._datagrid_inline_adding){var a=e.find(".datagrid-row-inline-add");a.hasClass("datagrid-row-inline-add-hidden")&&a.removeClass("datagrid-row-inline-add-hidden"),a.find("input:not([readonly]),textarea:not([readonly])").first().focus()}}}),g=function(){var t=l(".selectpicker").first();if(l.fn.selectpicker)return l.fn.selectpicker.defaults={countSelectedText:t.data("i18n-selected"),iconBase:"",tickIcon:t.data("selected-icon-check")}},l((function(){return g()})),f=function(){if(l.fn.selectpicker)return l("[data-datagrid-multiselect-id]").each((function(){var t;if(l(this).hasClass("selectpicker"))return l(this).removeAttr("id"),t=l(this).data("datagrid-multiselect-id"),l(this).on("loaded.bs.select",(function(t){return l(this).parent().attr("style","display:none;"),l(this).parent().find(".hidden").removeClass("hidden").addClass("btn-default btn-secondary")})),l(this).on("rendered.bs.select",(function(e){return l(this).parent().attr("id",t)}))}))},l((function(){return f()})),i("datagrid.fitlerMultiSelect",{success:function(){if(g(),l.fn.selectpicker)return l(".selectpicker").selectpicker({iconBase:"fa"})}}),i("datagrid.groupActionMultiSelect",{success:function(){return f()}}),i("datagrid.inline-editing",{success:function(t){if(t._datagrid_inline_editing)return l(".datagrid-"+t._datagrid_name).find(".datagrid-inline-edit-trigger").addClass("hidden")}}),i("datagrid.redraw-item",{success:function(t){if(t._datagrid_redraw_item_class)return l("tr[data-id="+t._datagrid_redraw_item_id+"]").attr("class",t._datagrid_redraw_item_class)}}),i("datagrid.reset-filter-by-column",{success:function(t){var e,a,i,n,r,d;if(t._datagrid_name&&((e=l(".datagrid-"+t._datagrid_name)).find("[data-datagrid-reset-filter-by-column]").addClass("hidden"),t.non_empty_filters&&t.non_empty_filters.length)){for(i=0,r=(d=t.non_empty_filters).length;i<r;i++)n=d[i],e.find("[data-datagrid-reset-filter-by-column="+n+"]").removeClass("hidden");return a=e.find(".reset-filter").attr("href"),e.find("[data-datagrid-reset-filter-by-column]").each((function(){var e;return n=l(this).attr("data-datagrid-reset-filter-by-column"),e=a.replace("do="+t._datagrid_name+"-resetFilter","do="+t._datagrid_name+"-resetColumnFilter"),e+="&"+t._datagrid_name+"-key="+n,l(this).attr("href",e)}))}}})}},t=>{t.O(0,[806,408],(()=>{return e=2425,t(t.s=e);var e}));t.O()}]);