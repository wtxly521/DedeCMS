﻿CKEDITOR.plugins.add("dedepagebreak",{icons:"dedepagebreak",init:function(a){a.addCommand("insertDedePageBreak",{exec:function(a){a.insertHtml("#p#分页标题#e#")}});a.ui.addButton("DedePageBreak",{label:"Insert PageBreak",command:"insertDedePageBreak",toolbar:"insert"})}});