CommentPress.textselector=new function(){var a=this;this.container="";this.set_container=function(b){this.container=b};this.get_container=function(){return this.container};this.get_selection=function(){var b;b={text:"",start:0,end:0};if(window.getSelection){b.text=window.getSelection().toString()}else{if(document.selection&&document.selection.type!="Control"){b.text=document.selection.createRange().text}}return b};this.clear_selection=function(){if(window.getSelection){if(window.getSelection().empty){window.getSelection().empty()}else{if(window.getSelection().removeAllRanges){window.getSelection().removeAllRanges()}}}else{if(document.selection){document.selection.empty()}}};this.add_text_selection=function(c,b){if(b=="prepend"){content=jQuery("#comment").val()}else{content=""}setTimeout(function(){jQuery("#comment").val("<strong>["+c+"]</strong>\n\n"+content)},200)};this.add_tinymce_selection=function(c,b){if(b=="prepend"){content=tinymce.activeEditor.getContent()}else{content=""}tinymce.activeEditor.setContent("<p><strong>["+c+"]</strong></p>"+content,{format:"html"});setTimeout(function(){tinymce.activeEditor.selection.select(tinymce.activeEditor.getBody(),true);tinymce.activeEditor.selection.collapse(false);tinymce.activeEditor.focus()},200)};this.selection={};this.save_selection=function(b){var c=a.store_selection(document.getElementById(b));if(!(b in this.selection)){this.selection[b]=[]}this.selection[b].push(c)};this.recall_selection=function(c){if(c in this.selection){for(var b=0,d;d=this.selection[c][b++];){a.restore_selection(document.getElementById(c),d);jQuery("#"+c).wrapSelection({fitToWord:false}).addClass("inline-highlight")}}};if(window.getSelection&&document.createRange){this.store_selection=function(d){var c=window.getSelection().getRangeAt(0);var b=c.cloneRange();b.selectNodeContents(d);b.setEnd(c.startContainer,c.startOffset);var e=b.toString().length;return{start:e,end:e+c.toString().length}};this.restore_selection=function(k,l){var b=0,h=document.createRange();h.setStart(k,0);h.collapse(true);var g=[k],d,e=false,m=false;while(!m&&(d=g.pop())){if(d.nodeType==3){var j=b+d.length;if(!e&&l.start>=b&&l.start<=j){h.setStart(d,l.start-b);e=true}if(e&&l.end>=b&&l.end<=j){h.setEnd(d,l.end-b);m=true}b=j}else{var f=d.childNodes.length;while(f--){g.push(d.childNodes[f])}}}var c=window.getSelection();c.removeAllRanges();c.addRange(h)}}else{if(document.selection&&document.body.createTextRange){this.store_selection=function(c){var b=document.selection.createRange();var d=document.body.createTextRange();d.moveToElementText(c);d.setEndPoint("EndToStart",b);var e=d.text.length;return{start:e,end:e+b.text.length}};this.restore_selection=function(d,c){var b=document.body.createTextRange();b.moveToElementText(d);b.collapse(true);b.moveEnd("character",c.end);b.moveStart("character",c.start);b.select()}}}this.highlighter_activate=function(){jQuery(".textblock").highlighter({selector:".holder",minWords:1,complete:function(b){}})};this.highlighter_deactivate=function(){jQuery(".textblock").highlighter("destroy")};this.highlighter_deactivate=function(){jQuery(".textblock").highlighter("destroy")};this.init=function(){a.highlighter_activate();jQuery(".textblock").click(function(){var b,c;c=a.get_container();b=jQuery(this).prop("id");console.log("textblock clicked");console.log(c);console.log(b);console.log(jQuery(this));if(c!=b){a.set_container(b);jQuery(".inline-highlight").each(function(d){var e=jQuery(this).contents();jQuery(this).replaceWith(e)});a.highlighter_deactivate();a.recall_selection(b);a.highlighter_activate()}});jQuery(".holder").mousedown(function(){return false});jQuery(".btn-left-comment").click(function(){var e,d,b,c;jQuery(".holder").hide();e=a.get_container();a.save_selection(e);d=a.get_selection();cp_scroll_comments(jQuery("#respond"),cp_scroll_speed);c=jQuery("#"+e).wrapSelection({fitToWord:false}).addClass("inline-highlight");return false});jQuery(".btn-left-quote").click(function(){var e,d,b,c;jQuery(".holder").hide();e=a.get_container();a.save_selection(e);d=a.get_selection();if(cp_tinymce=="1"){if(jQuery("#wp-comment-wrap").hasClass("html-active")){a.add_text_selection(d.text)}else{a.add_tinymce_selection(d.text)}}else{a.add_text_selection(d.text)}cp_scroll_comments(jQuery("#respond"),cp_scroll_speed);c=jQuery("#"+e).wrapSelection({fitToWord:false}).addClass("inline-highlight");return false});jQuery(".btn-right").click(function(){jQuery(".holder").hide();var b="";a.set_container(b);return false})}};jQuery(document).ready(function(a){a(document).on("commentpress-document-ready",function(b){CommentPress.textselector.init()});a(document).on("commentpress-reset-actions",function(b){})});