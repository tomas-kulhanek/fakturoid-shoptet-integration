"use strict";(self.webpackChunk=self.webpackChunk||[]).push([[143],{8144:(e,o,n)=>{n(4812),n(3734),n(3752),n(2272);var t=n(4609),i=(n(300),n(9127)),a=n.n(i),c=n(9755);window.LiveForm=a().LiveForm,window.Nette=a().Nette,document.addEventListener("DOMContentLoaded",t.default.initialize.bind(t.default)),document.addEventListener("DOMContentLoaded",(function(){window.Nette.init(),window.LiveForm.setOptions({controlErrorClass:"is-invalid",controlValidClass:"is-valid",messageErrorClass:"error invalid-feedback",messageTag:"span",showValid:!0,messageErrorPrefix:'&nbsp;<i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;'}),c("*[clipboard-copy-target-id]").on("click",(function(){var e=c(this).attr("clipboard-copy-target-id");!function(e){if(!navigator.clipboard)return void function(e){var o=document.createElement("textarea");o.value=e,o.style.top="0",o.style.left="0",o.style.position="fixed",document.body.appendChild(o),o.focus(),o.select();try{var n=document.execCommand("copy")?"successful":"unsuccessful";console.log("Fallback: Copying text command was "+n)}catch(e){console.error("Fallback: Oops, unable to copy",e)}document.body.removeChild(o)}(e);navigator.clipboard.writeText(e).then((function(){console.log("Async: Copying to clipboard was successful!")}),(function(e){console.error("Async: Could not copy text: ",e)}))}(c("#"+e).text())})),c(".numberLineId").on("change",(function(){c(this).val().length>0&&c("input.synchronize_proformaInvoices").prop("checked",!1)})),c("input.synchronize_proformaInvoices").on("change",(function(){c(this).prop("checked")&&c(".numberLineId").val().length>0&&(alert("Zálohové doklady nelze využívat, pokud máte jinou, než výchozí číselnou řadu."),c(this).prop("checked",!1))}))}))}},e=>{e.O(0,[615,437,725],(()=>{return o=8144,e(e.s=o);var o}));e.O()}]);