addComment={moveForm:function(k,j,a,h,c){this.disableForm();var b;var g=this.I(k);var l=this.I(a);var f=this.I("cancel-comment-reply-link");var i=this.I("comment_parent");var d=this.I("comment_post_ID");if(this.I("text_signature")){var e=this.I("text_signature")}else{var e=""}if(!g||!l||!f||!i){this.enableForm();return}if(d&&h){d.value=h}i.value=j;if(e!==""){e.value=c}this.respondID=a;addComment.setTitle(j,c,"set");if(!this.I("wp-temp-form-div")){b=document.createElement("div");b.id="wp-temp-form-div";b.style.display="none";l.parentNode.insertBefore(b,l)}g.parentNode.insertBefore(l,g.nextSibling);if(cp_special_page!="1"&&cp_promote_reading=="0"&&j=="0"){f.style.display="none"}else{f.style.display=""}f.onclick=function(){return addComment.cancelForm()};if(cp_tinymce=="1"){this.enableForm()}else{}l.style.display="block";addComment.clearCommentHighlight(this.parentID);addComment.highlightComment(j);this.text_signature=c;this.parentID=j;return false},moveFormToPara:function(d,b,a){var c="reply_to_para-"+d;addComment.moveForm(c,"0","respond",a,b);return false},cancelForm:function(){var c=addComment.I("wp-temp-form-div");var j=addComment.I(addComment.respondID);var e=this.I("cancel-comment-reply-link");if(!c||!j){return}addComment.clearCommentHighlight(this.parentID);if(cp_special_page!="1"){var i="";var h="";if(addComment.I("text_signature")){i=addComment.I("text_signature").value;addComment.I("text_signature").value="";var a=jQuery("#para_wrapper-"+i+" .reply_to_para").attr("id");if(a===undefined){var f=jQuery("#respond").closest("div.paragraph_wrapper");if(f.length>0){var d=f.attr("id");var a=jQuery("#"+d+" .reply_to_para").attr("id")}}h=a.split("-")[1]}if(cp_promote_reading=="1"){if(j.style.display!="none"){j.style.display="none"}}else{var b=addComment.I("comment_post_ID").value;addComment.moveFormToPara(h,i,b);return false}}else{}addComment.disableForm();var g=addComment.I("comment_parent").value;addComment.I("comment_parent").value="0";c.parentNode.insertBefore(j,c);c.parentNode.removeChild(c);e.style.display="none";e.onclick=null;addComment.setTitle("0",i,"cancel");this.text_signature="";addComment.enableForm();return false},I:function(a){return document.getElementById(a)},enableForm:function(){if(cp_tinymce=="1"){setTimeout(function(){tinyMCE.execCommand("mceAddControl",false,"comment");tinyMCE.execCommand("render")},1)}},disableForm:function(){if(cp_tinymce=="1"){tinyMCE.execCommand("mceRemoveControl",false,"comment")}},setTitle:function(f,a,e){var d=addComment.I("respond_title");if(f===undefined||f=="0"){if(a===undefined||a==""){if(cp_special_page=="1"){d.innerHTML="Leave a comment"}else{d.innerHTML=jQuery("#para_wrapper-"+a+" a.reply_to_para").text();var g=jQuery("#para_wrapper-"+addComment.text_signature+" .commentlist");if(g[0]&&cp_promote_reading=="0"){jQuery("#para_wrapper-"+addComment.text_signature+" div.reply_to_para").show()}if(e=="cancel"&&cp_promote_reading=="1"){jQuery("div.reply_to_para").show()}else{jQuery("#para_wrapper-"+a+" div.reply_to_para").hide()}}}else{d.innerHTML=jQuery("#para_wrapper-"+a+" a.reply_to_para").text();var g=jQuery("#para_wrapper-"+addComment.text_signature+" .commentlist");if((g[0]&&cp_promote_reading=="0")||cp_promote_reading=="1"){if(addComment.text_signature!==undefined){jQuery("#para_wrapper-"+addComment.text_signature+" div.reply_to_para").show()}}if(cp_promote_reading=="0"){jQuery("#para_wrapper-"+a+" div.reply_to_para").hide()}else{if(e=="cancel"){jQuery("div.reply_to_para").show()}else{jQuery("#para_wrapper-"+a+" div.reply_to_para").toggle()}}}}else{var b=jQuery("#comment-"+f+" > .reply")[0];var c=jQuery(b).text();if(c!=""){d.innerHTML=c;if(a===undefined||a==""){a==""}if(cp_promote_reading=="1"){if(addComment.text_signature!==undefined){jQuery("#para_wrapper-"+addComment.text_signature+" div.reply_to_para").show()}jQuery("#para_wrapper-"+a+" div.reply_to_para").show()}}}},highlightComment:function(a){if(a!="0"){jQuery("#comment-"+a+" > .reply").css("display","none")}jQuery("#li-comment-"+a+" > .comment-wrapper").addClass("background-highlight")},clearCommentHighlight:function(a){if(a!="0"){jQuery("#comment-"+a+" > .reply").css("display","block")}jQuery("#li-comment-"+a+" > .comment-wrapper").removeClass("background-highlight")},clearAllCommentHighlights:function(){jQuery(".reply").css("display","block");jQuery(".comment-wrapper").removeClass("background-highlight")},getTextSig:function(){return this.text_signature},getLevel:function(){if(this.parentID===undefined||this.parentID==="0"){return true}else{return false}}};