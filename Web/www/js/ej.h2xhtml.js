function ej_set_xhtml(data,safeMode,useRGB){return new ejH2X(data,safeMode,useRGB).parse()};function ejH2X(data,safeMode,useRGB){this.data=data;this.safeMode=safeMode;this.useRGB=useRGB;if(typeof this.safeMode=='undefined'){this.safeMode=true}if(typeof this.useRGB=='undefined'){this.useRGB=false}};ejH2X.prototype.setHTML=function(data){this.data=data||this.data};ejH2X.prototype.parse=function(){var state=0;var xhtml='';var p=0;var unget=false;var tagname='';var attrname='';var attrval='';var quot='';var data=this.data;var len=data.length;var quotval='';var tagtype=0;var inpre=false;var codetype='';var harm_tag=false;var harm_attr=false;var safe_mode=this.safeMode;var c='';var use_rgb=this.useRGB;var retstate=0;var code='';var not_analyze='';var reg_event=new RegExp('^on[a-z]+$','ig');var reg_js=new RegExp('javascript\:.*','ig');var reg_alt=new RegExp('alt\=*','ig');var reg_src=new RegExp('^(?:(ht|f)tp(?:s)?:\\/\\/)?(?:[\.]{1,2})?[^ \\?&=\"\\n\\r\\t<]*\.((jpg|png|gif|tif|fla)|swf(?:\\?[^ <\\.\\?\\"\\n\\r\\t]*)?)$','ig');while(1){if(p>=len&&!unget){return xhtml}if(unget){unget=false}else{c=data.substr(p++,1)}switch(state){case 0:if(c=='<'){state=1;break}xhtml+=c;break;case 1:if(/[a-zA-Z]/.test(c)){state=2;tagtype=1;tagname=c.toLowerCase();break}if(c=='/'){state=2;tagtype=-1;break}if(c=='!'){if(data.substr(p,2)=='--'){xhtml+='<!--';p+=2;state=9;break}xhtml+='<!';state=10;break}if(c=='?'||c=='%'){codetype=c;state=11;code='<'+c;break}xhtml+=c;unget=true;state=0;break;case 2:if(ejH2X.isSpaceChar[c]){if(safe_mode&&ejH2X.isHarmTag[tagname]){if(tagtype>0){harm_tag=true}state=3;break}xhtml+=(tagtype>0?'<':'</')+tagname;state=3;break}if(c=='/'){if(safe_mode&&ejH2X.isHarmTag[tagname]){if(tagtype>0){harm_tag=true}state=3;break}xhtml+=(tagtype>0?'<':'</')+tagname;if(data.substr(p,1)!='>'){state=3;break}state=4;break}if(c=='>'){if(safe_mode&&ejH2X.isHarmTag[tagname]){if(tagtype>0){harm_tag=true}else{unget=true}state=4;break}xhtml+=(tagtype>0?'<':'</')+tagname;unget=true;state=4;break}var nxt=data.substr(p,1);if(c=='<'&&(nxt=='?'||nxt=='%')){codetype=nxt;state=11;retstate=2;p++;code=c+nxt;break}tagname+=c.toLowerCase();break;case 3:if(ejH2X.isSpaceChar[c]){break}if(c=='/'){if(data.substr(p,1)!='>'){break}state=4;break}if(c=='>'){unget=true;state=4;break}var nxt=data.substr(p,1);if(c=='<'&&(nxt=='?'||nxt=='%')){codetype=nxt;state=11;retstate=3;p++;code=c+nxt;break}attrname=c.toLowerCase();attrval='';state=5;break;case 4:if(!safe_mode||!ejH2X.isHarmTag[tagname]){xhtml+=ejH2X.isEmptyTag[tagname]?' />':'>'}if(tagtype>0&&ejH2X.dontAnalyzeContent[tagname]){state=13;attrname=attrval=quot='';tagtype=0;break}if(tagname=='pre'){inpre=!inpre}state=0;tagname=attrname=attrval=quot='';tagtype=0;break;case 5:var ma=attrname.match(reg_event);if(ejH2X.isSpaceChar[c]){if(!harm_tag){if(!safe_mode||!ma){xhtml+=' '+attrname;xhtml+='="'+attrname+'"'}}state=3;break}if(c=='/'){if(!harm_tag){if(!safe_mode||!ma){xhtml+=' '+attrname;xhtml+='="'+attrname+'"'}}if(data.substr(p,1)!='>'){state=3;break}state=4;break}if(c=='>'){if(!harm_tag){if(!safe_mode||!ma){xhtml+=' '+attrname;xhtml+='="'+attrname+'"'}}unget=true;state=4;break}if(c=='='){if(!harm_tag){if(safe_mode&&ma){harm_attr=true}else{xhtml+=' '+attrname+'='}}state=6;break}var nxt=data.substr(p,1);if(c=='<'&&(nxt=='?'||nxt=='%')){codetype=nxt;state=11;retstate=5;p++;code=c+nxt;break}if(c=='"'||c=="'"){attrname+='\\'+c}else{attrname+=c.toLowerCase()}break;case 6:if(ejH2X.isSpaceChar[c]){if(!harm_tag){if(!harm_attr){xhtml+=(ejH2X.isEmptyAttr[attrname]?'"'+attrname+'"':'""')}}harm_attr=false;state=3;break}if(c=='>'){if(!harm_tag){if(!harm_attr){xhtml+=(ejH2X.isEmptyAttr[attrname]?'"'+attrname+'"':'""')}}unget=true;harm_attr=false;state=4;break}if(c=='/'&&data.substr(p,1)=='>'){if(!harm_tag){if(!harm_attr){xhtml+=(ejH2X.isEmptyAttr[attrname]?'"'+attrname+'"':'""')}}harm_attr=false;state=4;break}if(c=='"'||c=="'"){quot=c;state=8;break}var nxt=data.substr(p,1);if(c=='<'&&(nxt=='?'||nxt=='%')){codetype=nxt;state=11;retstate=7;p++;code=c+nxt;break}attrval=c;state=7;break;case 7:if(ejH2X.isSpaceChar[c]){if(!harm_tag){if(safe_mode&&tagname=='a'&&attrname=='href'&&attrval.match(reg_js)){attrval='_js_'}if(safe_mode&&tagname=='img'&&attrname=='src'&&attrval.match(reg_js)){attrval='_js_'}if(safe_mode&&attrname=='src'&&!attrval.match(reg_src)){attrval='_src_'}if(!use_rgb){attrval=attrval.replace(/rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)/ig,function(s){return ejH2X.RGB2HEX(s)})}if(!harm_attr){xhtml+='"'+ejH2X.escapeQuot(attrval,'"')+'"'}}harm_attr=false;state=3;break}if(c=='/'&&data.substr(p,1)=='>'){if(!harm_tag){if(safe_mode&&tagname=='a'&&attrname=='href'&&attrval.match(reg_js)){attrval='_js_'}if(safe_mode&&tagname=='img'&&attrname=='src'&&attrval.match(reg_js)){attrval='_js_'}if(safe_mode&&attrname=='src'&&!attrval.match(reg_src)){attrval='_src_'}if(!use_rgb){attrval=attrval.replace(/rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)/ig,function(s){return ejH2X.RGB2HEX(s)})}if(!harm_attr){xhtml+='"'+ejH2X.escapeQuot(attrval,'"')+'"'}}harm_attr=false;state=4;break}if(c=='>'){unget=true;if(!harm_tag){if(safe_mode&&tagname=='a'&&attrname=='href'&&attrval.match(reg_js)){attrval='_js_'}if(safe_mode&&tagname=='img'&&attrname=='src'&&attrval.match(reg_js)){attrval='_js_'}if(safe_mode&&attrname=='src'&&!attrval.match(reg_src)){attrval='_src_'}if(!use_rgb){attrval=attrval.replace(/rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)/ig,function(s){return ejH2X.RGB2HEX(s)})}if(!harm_attr){xhtml+='"'+ejH2X.escapeQuot(attrval,'"')+'"'}}if(tagname=='img'&&attrname!='alt'){xhtml+=' alt=""'}if(tagname=='textarea'&&attrname!='cols'){xhtml+=' cols=""'}if(tagname=='textarea'&&attrname!='rows'){xhtml+=' rows=""'}harm_attr=false;state=4;break}var nxt=data.substr(p,1);if(c=='<'&&(nxt=='?'||nxt=='%')){codetype=nxt;state=11;retstate=7;p++;code=c+nxt;break}attrval+=c;break;case 8:if(c==quot){if(!harm_tag){if(safe_mode&&tagname=='a'&&attrname=='href'&&attrval.match(reg_js)){attrval='_js_'}if(safe_mode&&tagname=='img'&&attrname=='src'&&attrval.match(reg_js)){attrval='_js_'}if(safe_mode&&attrname=='src'&&!attrval.match(reg_src)){attrval='_src_'}if(!use_rgb){attrval=attrval.replace(/rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)/ig,function(s){return ejH2X.RGB2HEX(s)})}if(!harm_attr){xhtml+='"'+ejH2X.escapeQuot(attrval,'"')+'"'}}harm_attr=false;state=3;break}var nxt=data.substr(p,1);if(c=='<'&&(nxt=='?'||nxt=='%')){codetype=nxt;state=11;retstate=8;p++;code=c+nxt;break}attrval+=c;break;case 9:if(c=='-'&&data.substr(p,2)=='->'){p+=2;xhtml+='-->';state=0;break}var nxt=data.substr(p,1);if(c=='<'&&(nxt=='?'||nxt=='%')){codetype=nxt;state=11;retstate=9;p++;code=c+nxt;break}xhtml+=c;break;case 10:if(c=='>'){state=0}var nxt=data.substr(p,1);if(c=='<'&&(nxt=='?'||nxt=='%')){codetype=nxt;state=11;retstate=10;p++;code=c+nxt;break}xhtml+=c;break;case 11:if(c=="'"||c=='"'){quot=c;state=12;break}if(c==codetype&&data.substr(p,1)=='>'){if(retstate>0){state=retstate;retstate=0}else{state=0}code+=c+'>';if(!safe_mode){if(state==2){tagname+=code}else if(state==3){xhtml+=' '+code}else if(state==5){attrname+=code}else if(state==7||state==8){attrval+=code}else if(state==13){not_analyze+=code}else{xhtml+=code}}codetype='';code='';p++;break}code+=c;break;case 12:if(c==quot){state=11;code+=quot+ejH2X.escapeQuot(quotval,quot)+quot;quotval=quot='';break}quotval+=c;break;case 13:if(c=='<'&&data.substr(p,tagname.length+1).toLowerCase()=='/'+tagname){if(!harm_tag){xhtml+=not_analyze}not_analyze='';harm_tag=false;unget=true;state=0;tagname='';break}var nxt=data.substr(p,1);if(c=='<'&&(nxt=='?'||nxt=='%')){codetype=nxt;state=11;retstate=13;p++;code=c+nxt;break}if(tagname=='textarea'){not_analyze+=ejH2X.escapeHTMLChar(c)}else{not_analyze+=c}break}}return xhtml};ejH2X.escapeQuot=function(str,quot){if(!quot){quot='"'}if(quot=='"'){return str.replace(/"/ig,'\\"')}return str.replace(/'/ig,"\\'")};ejH2X.escapeHTMLChar=function(c){if(c=='&'){return'&amp;'}if(c=='<'){return'&lt;'}if(c=='>'){return'&gt;'}return c};ejH2X.RGB2HEX=function(s){var res=s.split(',');return'#'+ejH2X.N2HEX(parseInt(res[0].substring(4)))+ejH2X.N2HEX(parseInt(res[1]))+ejH2X.N2HEX(parseInt(res[2].replace(')','')))};ejH2X.N2HEX=function(n){if(n>255){return''}else{b=n/16;r=n%16;b=b-(r/16);base=ejH2X.HEX(b);rem=ejH2X.HEX(r);return''+base+rem}};ejH2X.HEX=function(x){if(x>=0&&x<=9){return x}else{switch(x){case 10:return"A";case 11:return"B";case 12:return"C";case 13:return"D";case 14:return"E";case 15:return"F"}}};ejH2X.isSpaceChar={' ':1,'\r':1,'\n':1,'\t':1};ejH2X.isHarmTag={'script':1,'iframe':1,'meta':1,'style':1};ejH2X.isEmptyTag={'area':1,'base':1,'basefont':1,'br':1,'hr':1,'img':1,'input':1,'link':1,'meta':1,'param':1};ejH2X.isEmptyAttr={'checked':1,'compact':1,'declare':1,'defer':1,'disabled':1,'ismap':1,'multiple':1,'noresize':1,'nosave':1,'noshade':1,'nowrap':1,'readonly':1,'selected':1};ejH2X.dontAnalyzeContent={'textarea':1,'script':1,'style':1};