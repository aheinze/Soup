var Handlebars={VERSION:"1.0.beta.4",helpers:{},partials:{},registerHelper:function(d,b,a){if(a)b.not=a;this.helpers[d]=b},registerPartial:function(d,b){this.partials[d]=b}};Handlebars.registerHelper("helperMissing",function(d){if(2!==arguments.length)throw Error("Could not find property '"+d+"'");});
Handlebars.registerHelper("blockHelperMissing",function(d,b){var a=b.inverse||function(){},g=b.fn,h="",e=Object.prototype.toString.call(d);"[object Function]"===e&&(d=d());if(!0===d)return g(this);if(!1===d||null==d)return a(this);if("[object Array]"===e){if(0<d.length){a=0;for(e=d.length;a<e;a++)h+=g(d[a])}else h=a(this);return h}return g(d)});Handlebars.registerHelper("each",function(d,b){var a=b.fn,g=b.inverse,h="";if(d&&0<d.length)for(var g=0,e=d.length;g<e;g++)h+=a(d[g]);else h=g(this);return h});
Handlebars.registerHelper("if",function(d,b){return!d||Handlebars.Utils.isEmpty(d)?b.inverse(this):b.fn(this)});Handlebars.registerHelper("unless",function(d,b){var a=b.fn;b.fn=b.inverse;b.inverse=a;return Handlebars.helpers["if"].call(this,d,b)});Handlebars.registerHelper("with",function(d,b){return b.fn(d)});Handlebars.registerHelper("log",function(d){Handlebars.log(d)});
var handlebars=function(){var d={trace:function(){},yy:{},symbols_:{error:2,root:3,program:4,EOF:5,statements:6,simpleInverse:7,statement:8,openInverse:9,closeBlock:10,openBlock:11,mustache:12,partial:13,CONTENT:14,COMMENT:15,OPEN_BLOCK:16,inMustache:17,CLOSE:18,OPEN_INVERSE:19,OPEN_ENDBLOCK:20,path:21,OPEN:22,OPEN_UNESCAPED:23,OPEN_PARTIAL:24,params:25,hash:26,param:27,STRING:28,INTEGER:29,BOOLEAN:30,hashSegments:31,hashSegment:32,ID:33,EQUALS:34,pathSegments:35,SEP:36,$accept:0,$end:1},terminals_:{2:"error",
5:"EOF",14:"CONTENT",15:"COMMENT",16:"OPEN_BLOCK",18:"CLOSE",19:"OPEN_INVERSE",20:"OPEN_ENDBLOCK",22:"OPEN",23:"OPEN_UNESCAPED",24:"OPEN_PARTIAL",28:"STRING",29:"INTEGER",30:"BOOLEAN",33:"ID",34:"EQUALS",36:"SEP"},productions_:[0,[3,2],[4,3],[4,1],[4,0],[6,1],[6,2],[8,3],[8,3],[8,1],[8,1],[8,1],[8,1],[11,3],[9,3],[10,3],[12,3],[12,3],[13,3],[13,4],[7,2],[17,3],[17,2],[17,2],[17,1],[25,2],[25,1],[27,1],[27,1],[27,1],[27,1],[26,1],[31,2],[31,1],[32,3],[32,3],[32,3],[32,3],[21,1],[35,3],[35,1]],performAction:function(a,
b,d,e,f,c){a=c.length-1;switch(f){case 1:return c[a-1];case 2:this.$=new e.ProgramNode(c[a-2],c[a]);break;case 3:this.$=new e.ProgramNode(c[a]);break;case 4:this.$=new e.ProgramNode([]);break;case 5:this.$=[c[a]];break;case 6:c[a-1].push(c[a]);this.$=c[a-1];break;case 7:this.$=new e.InverseNode(c[a-2],c[a-1],c[a]);break;case 8:this.$=new e.BlockNode(c[a-2],c[a-1],c[a]);break;case 9:this.$=c[a];break;case 10:this.$=c[a];break;case 11:this.$=new e.ContentNode(c[a]);break;case 12:this.$=new e.CommentNode(c[a]);
break;case 13:this.$=new e.MustacheNode(c[a-1][0],c[a-1][1]);break;case 14:this.$=new e.MustacheNode(c[a-1][0],c[a-1][1]);break;case 15:this.$=c[a-1];break;case 16:this.$=new e.MustacheNode(c[a-1][0],c[a-1][1]);break;case 17:this.$=new e.MustacheNode(c[a-1][0],c[a-1][1],!0);break;case 18:this.$=new e.PartialNode(c[a-1]);break;case 19:this.$=new e.PartialNode(c[a-2],c[a-1]);break;case 21:this.$=[[c[a-2]].concat(c[a-1]),c[a]];break;case 22:this.$=[[c[a-1]].concat(c[a]),null];break;case 23:this.$=[[c[a-
1]],c[a]];break;case 24:this.$=[[c[a]],null];break;case 25:c[a-1].push(c[a]);this.$=c[a-1];break;case 26:this.$=[c[a]];break;case 27:this.$=c[a];break;case 28:this.$=new e.StringNode(c[a]);break;case 29:this.$=new e.IntegerNode(c[a]);break;case 30:this.$=new e.BooleanNode(c[a]);break;case 31:this.$=new e.HashNode(c[a]);break;case 32:c[a-1].push(c[a]);this.$=c[a-1];break;case 33:this.$=[c[a]];break;case 34:this.$=[c[a-2],c[a]];break;case 35:this.$=[c[a-2],new e.StringNode(c[a])];break;case 36:this.$=
[c[a-2],new e.IntegerNode(c[a])];break;case 37:this.$=[c[a-2],new e.BooleanNode(c[a])];break;case 38:this.$=new e.IdNode(c[a]);break;case 39:c[a-2].push(c[a]);this.$=c[a-2];break;case 40:this.$=[c[a]]}},table:[{3:1,4:2,5:[2,4],6:3,8:4,9:5,11:6,12:7,13:8,14:[1,9],15:[1,10],16:[1,12],19:[1,11],22:[1,13],23:[1,14],24:[1,15]},{1:[3]},{5:[1,16]},{5:[2,3],7:17,8:18,9:5,11:6,12:7,13:8,14:[1,9],15:[1,10],16:[1,12],19:[1,19],20:[2,3],22:[1,13],23:[1,14],24:[1,15]},{5:[2,5],14:[2,5],15:[2,5],16:[2,5],19:[2,
5],20:[2,5],22:[2,5],23:[2,5],24:[2,5]},{4:20,6:3,8:4,9:5,11:6,12:7,13:8,14:[1,9],15:[1,10],16:[1,12],19:[1,11],20:[2,4],22:[1,13],23:[1,14],24:[1,15]},{4:21,6:3,8:4,9:5,11:6,12:7,13:8,14:[1,9],15:[1,10],16:[1,12],19:[1,11],20:[2,4],22:[1,13],23:[1,14],24:[1,15]},{5:[2,9],14:[2,9],15:[2,9],16:[2,9],19:[2,9],20:[2,9],22:[2,9],23:[2,9],24:[2,9]},{5:[2,10],14:[2,10],15:[2,10],16:[2,10],19:[2,10],20:[2,10],22:[2,10],23:[2,10],24:[2,10]},{5:[2,11],14:[2,11],15:[2,11],16:[2,11],19:[2,11],20:[2,11],22:[2,
11],23:[2,11],24:[2,11]},{5:[2,12],14:[2,12],15:[2,12],16:[2,12],19:[2,12],20:[2,12],22:[2,12],23:[2,12],24:[2,12]},{17:22,21:23,33:[1,25],35:24},{17:26,21:23,33:[1,25],35:24},{17:27,21:23,33:[1,25],35:24},{17:28,21:23,33:[1,25],35:24},{21:29,33:[1,25],35:24},{1:[2,1]},{6:30,8:4,9:5,11:6,12:7,13:8,14:[1,9],15:[1,10],16:[1,12],19:[1,11],22:[1,13],23:[1,14],24:[1,15]},{5:[2,6],14:[2,6],15:[2,6],16:[2,6],19:[2,6],20:[2,6],22:[2,6],23:[2,6],24:[2,6]},{17:22,18:[1,31],21:23,33:[1,25],35:24},{10:32,20:[1,
33]},{10:34,20:[1,33]},{18:[1,35]},{18:[2,24],21:40,25:36,26:37,27:38,28:[1,41],29:[1,42],30:[1,43],31:39,32:44,33:[1,45],35:24},{18:[2,38],28:[2,38],29:[2,38],30:[2,38],33:[2,38],36:[1,46]},{18:[2,40],28:[2,40],29:[2,40],30:[2,40],33:[2,40],36:[2,40]},{18:[1,47]},{18:[1,48]},{18:[1,49]},{18:[1,50],21:51,33:[1,25],35:24},{5:[2,2],8:18,9:5,11:6,12:7,13:8,14:[1,9],15:[1,10],16:[1,12],19:[1,11],20:[2,2],22:[1,13],23:[1,14],24:[1,15]},{14:[2,20],15:[2,20],16:[2,20],19:[2,20],22:[2,20],23:[2,20],24:[2,
20]},{5:[2,7],14:[2,7],15:[2,7],16:[2,7],19:[2,7],20:[2,7],22:[2,7],23:[2,7],24:[2,7]},{21:52,33:[1,25],35:24},{5:[2,8],14:[2,8],15:[2,8],16:[2,8],19:[2,8],20:[2,8],22:[2,8],23:[2,8],24:[2,8]},{14:[2,14],15:[2,14],16:[2,14],19:[2,14],20:[2,14],22:[2,14],23:[2,14],24:[2,14]},{18:[2,22],21:40,26:53,27:54,28:[1,41],29:[1,42],30:[1,43],31:39,32:44,33:[1,45],35:24},{18:[2,23]},{18:[2,26],28:[2,26],29:[2,26],30:[2,26],33:[2,26]},{18:[2,31],32:55,33:[1,56]},{18:[2,27],28:[2,27],29:[2,27],30:[2,27],33:[2,
27]},{18:[2,28],28:[2,28],29:[2,28],30:[2,28],33:[2,28]},{18:[2,29],28:[2,29],29:[2,29],30:[2,29],33:[2,29]},{18:[2,30],28:[2,30],29:[2,30],30:[2,30],33:[2,30]},{18:[2,33],33:[2,33]},{18:[2,40],28:[2,40],29:[2,40],30:[2,40],33:[2,40],34:[1,57],36:[2,40]},{33:[1,58]},{14:[2,13],15:[2,13],16:[2,13],19:[2,13],20:[2,13],22:[2,13],23:[2,13],24:[2,13]},{5:[2,16],14:[2,16],15:[2,16],16:[2,16],19:[2,16],20:[2,16],22:[2,16],23:[2,16],24:[2,16]},{5:[2,17],14:[2,17],15:[2,17],16:[2,17],19:[2,17],20:[2,17],22:[2,
17],23:[2,17],24:[2,17]},{5:[2,18],14:[2,18],15:[2,18],16:[2,18],19:[2,18],20:[2,18],22:[2,18],23:[2,18],24:[2,18]},{18:[1,59]},{18:[1,60]},{18:[2,21]},{18:[2,25],28:[2,25],29:[2,25],30:[2,25],33:[2,25]},{18:[2,32],33:[2,32]},{34:[1,57]},{21:61,28:[1,62],29:[1,63],30:[1,64],33:[1,25],35:24},{18:[2,39],28:[2,39],29:[2,39],30:[2,39],33:[2,39],36:[2,39]},{5:[2,19],14:[2,19],15:[2,19],16:[2,19],19:[2,19],20:[2,19],22:[2,19],23:[2,19],24:[2,19]},{5:[2,15],14:[2,15],15:[2,15],16:[2,15],19:[2,15],20:[2,
15],22:[2,15],23:[2,15],24:[2,15]},{18:[2,34],33:[2,34]},{18:[2,35],33:[2,35]},{18:[2,36],33:[2,36]},{18:[2,37],33:[2,37]}],defaultActions:{16:[2,1],37:[2,23],53:[2,21]},parseError:function(a){throw Error(a);},parse:function(a){function b(){var f;f=d.lexer.lex()||1;"number"!==typeof f&&(f=d.symbols_[f]||f);return f}var d=this,e=[0],f=[null],c=[],o=this.table,t="",p=0,u=0,m=0;this.lexer.setInput(a);this.lexer.yy=this.yy;this.yy.lexer=this.lexer;if("undefined"==typeof this.lexer.yylloc)this.lexer.yylloc=
{};a=this.lexer.yylloc;c.push(a);if("function"===typeof this.yy.parseError)this.parseError=this.yy.parseError;for(var i,k,j,l,q={},r,n;;){j=e[e.length-1];this.defaultActions[j]?l=this.defaultActions[j]:(null==i&&(i=b()),l=o[j]&&o[j][i]);if("undefined"===typeof l||!l.length||!l[0]){if(!m){k=[];for(r in o[j])this.terminals_[r]&&2<r&&k.push("'"+this.terminals_[r]+"'");var s="",s=this.lexer.showPosition?"Parse error on line "+(p+1)+":\n"+this.lexer.showPosition()+"\nExpecting "+k.join(", "):"Parse error on line "+
(p+1)+": Unexpected "+(1==i?"end of input":"'"+(this.terminals_[i]||i)+"'");this.parseError(s,{text:this.lexer.match,token:this.terminals_[i]||i,line:this.lexer.yylineno,loc:a,expected:k})}if(3==m){if(1==i)throw Error(s||"Parsing halted.");u=this.lexer.yyleng;t=this.lexer.yytext;p=this.lexer.yylineno;a=this.lexer.yylloc;i=b()}for(;!((2).toString()in o[j]);){if(0==j)throw Error(s||"Parsing halted.");e.length-=2;f.length-=1;c.length-=1;j=e[e.length-1]}k=i;i=2;j=e[e.length-1];l=o[j]&&o[j][2];m=3}if(l[0]instanceof
Array&&1<l.length)throw Error("Parse Error: multiple actions possible at state: "+j+", token: "+i);switch(l[0]){case 1:e.push(i);f.push(this.lexer.yytext);c.push(this.lexer.yylloc);e.push(l[1]);i=null;k?(i=k,k=null):(u=this.lexer.yyleng,t=this.lexer.yytext,p=this.lexer.yylineno,a=this.lexer.yylloc,0<m&&m--);break;case 2:n=this.productions_[l[1]][1];q.$=f[f.length-n];q._$={first_line:c[c.length-(n||1)].first_line,last_line:c[c.length-1].last_line,first_column:c[c.length-(n||1)].first_column,last_column:c[c.length-
1].last_column};j=this.performAction.call(q,t,u,p,this.yy,l[1],f,c);if("undefined"!==typeof j)return j;n&&(e=e.slice(0,-2*n),f=f.slice(0,-1*n),c=c.slice(0,-1*n));e.push(this.productions_[l[1]][0]);f.push(q.$);c.push(q._$);l=o[e[e.length-2]][e[e.length-1]];e.push(l);break;case 3:return!0}}return!0}},b=function(){return{EOF:1,parseError:function(a,b){if(this.yy.parseError)this.yy.parseError(a,b);else throw Error(a);},setInput:function(a){this._input=a;this._more=this._less=this.done=!1;this.yylineno=
this.yyleng=0;this.yytext=this.matched=this.match="";this.conditionStack=["INITIAL"];this.yylloc={first_line:1,first_column:0,last_line:1,last_column:0};return this},input:function(){var a=this._input[0];this.yytext+=a;this.yyleng++;this.match+=a;this.matched+=a;a.match(/\n/)&&this.yylineno++;this._input=this._input.slice(1);return a},unput:function(a){this._input=a+this._input;return this},more:function(){this._more=!0;return this},pastInput:function(){var a=this.matched.substr(0,this.matched.length-
this.match.length);return(20<a.length?"...":"")+a.substr(-20).replace(/\n/g,"")},upcomingInput:function(){var a=this.match;20>a.length&&(a+=this._input.substr(0,20-a.length));return(a.substr(0,20)+(20<a.length?"...":"")).replace(/\n/g,"")},showPosition:function(){var a=this.pastInput(),b=Array(a.length+1).join("-");return a+this.upcomingInput()+"\n"+b+"^"},next:function(){if(this.done)return this.EOF;if(!this._input)this.done=!0;var a,b;if(!this._more)this.match=this.yytext="";for(var d=this._currentRules(),
e=0;e<d.length;e++)if(a=this._input.match(this.rules[d[e]])){if(b=a[0].match(/\n.*/g))this.yylineno+=b.length;this.yylloc={first_line:this.yylloc.last_line,last_line:this.yylineno+1,first_column:this.yylloc.last_column,last_column:b?b[b.length-1].length-1:this.yylloc.last_column+a[0].length};this.yytext+=a[0];this.match+=a[0];this.matches=a;this.yyleng=this.yytext.length;this._more=!1;this._input=this._input.slice(a[0].length);this.matched+=a[0];if(a=this.performAction.call(this,this.yy,this,d[e],
this.conditionStack[this.conditionStack.length-1]))return a;return}if(""===this._input)return this.EOF;this.parseError("Lexical error on line "+(this.yylineno+1)+". Unrecognized text.\n"+this.showPosition(),{text:"",token:null,line:this.yylineno})},lex:function(){var a=this.next();return"undefined"!==typeof a?a:this.lex()},begin:function(a){this.conditionStack.push(a)},popState:function(){return this.conditionStack.pop()},_currentRules:function(){return this.conditions[this.conditionStack[this.conditionStack.length-
1]].rules},performAction:function(a,b,d){switch(d){case 0:this.begin("mu");if(b.yytext)return 14;break;case 1:return 14;case 2:return 24;case 3:return 16;case 4:return 20;case 5:return 19;case 6:return 19;case 7:return 23;case 8:return 23;case 9:return b.yytext=b.yytext.substr(3,b.yyleng-5),this.begin("INITIAL"),15;case 10:return 22;case 11:return 34;case 12:return 33;case 13:return 33;case 14:return 36;case 16:return this.begin("INITIAL"),18;case 17:return this.begin("INITIAL"),18;case 18:return b.yytext=
b.yytext.substr(1,b.yyleng-2).replace(/\\"/g,'"'),28;case 19:return 30;case 20:return 30;case 21:return 29;case 22:return 33;case 23:return b.yytext=b.yytext.substr(1,b.yyleng-2),33;case 24:return"INVALID";case 25:return 5}},rules:[/^[^\x00]*?(?=(\{\{))/,/^[^\x00]+/,/^\{\{>/,/^\{\{#/,/^\{\{\//,/^\{\{\^/,/^\{\{\s*else\b/,/^\{\{\{/,/^\{\{&/,/^\{\{![\s\S]*?\}\}/,/^\{\{/,/^=/,/^\.(?=[} ])/,/^\.\./,/^[/.]/,/^\s+/,/^\}\}\}/,/^\}\}/,/^"(\\["]|[^"])*"/,/^true(?=[}\s])/,/^false(?=[}\s])/,/^[0-9]+(?=[}\s])/,
/^[a-zA-Z0-9_$-]+(?=[=}\s/.])/,/^\[.*\]/,/^./,/^$/],conditions:{mu:{rules:[2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25],inclusive:!1},INITIAL:{rules:[0,1,25],inclusive:!0}}}}();d.lexer=b;return d}();
if("undefined"!==typeof require&&"undefined"!==typeof exports)exports.parser=handlebars,exports.parse=function(){return handlebars.parse.apply(handlebars,arguments)},exports.main=function(d){if(!d[1])throw Error("Usage: "+d[0]+" FILE");d="undefined"!==typeof process?require("fs").readFileSync(require("path").join(process.cwd(),d[1]),"utf8"):require("file").path(require("file").cwd()).join(d[1]).read({charset:"utf-8"});return exports.parser.parse(d)},"undefined"!==typeof module&&require.main===module&&
exports.main("undefined"!==typeof process?process.argv.slice(1):require("system").args);Handlebars.Parser=handlebars;Handlebars.parse=function(d){Handlebars.Parser.yy=Handlebars.AST;return Handlebars.Parser.parse(d)};Handlebars.print=function(d){return(new Handlebars.PrintVisitor).accept(d)};Handlebars.logger={DEBUG:0,INFO:1,WARN:2,ERROR:3,level:3,log:function(){}};Handlebars.log=function(d,b){Handlebars.logger.log(d,b)};
(function(){Handlebars.AST={};Handlebars.AST.ProgramNode=function(b,a){this.type="program";this.statements=b;if(a)this.inverse=new Handlebars.AST.ProgramNode(a)};Handlebars.AST.MustacheNode=function(b,a,d){this.type="mustache";this.id=b[0];this.params=b.slice(1);this.hash=a;this.escaped=!d};Handlebars.AST.PartialNode=function(b,a){this.type="partial";this.id=b;this.context=a};var d=function(b,a){if(b.original!==a.original)throw new Handlebars.Exception(b.original+" doesn't match "+a.original);};Handlebars.AST.BlockNode=
function(b,a,g){d(b.id,g);this.type="block";this.mustache=b;this.program=a};Handlebars.AST.InverseNode=function(b,a,g){d(b.id,g);this.type="inverse";this.mustache=b;this.program=a};Handlebars.AST.ContentNode=function(b){this.type="content";this.string=b};Handlebars.AST.HashNode=function(b){this.type="hash";this.pairs=b};Handlebars.AST.IdNode=function(b){this.type="ID";this.original=b.join(".");for(var a=[],d=0,h=0,e=b.length;h<e;h++){var f=b[h];".."===f?d++:"."===f||"this"===f?this.isScoped=!0:a.push(f)}this.parts=
a;this.string=a.join(".");this.depth=d;this.isSimple=1===a.length&&0===d};Handlebars.AST.StringNode=function(b){this.type="STRING";this.string=b};Handlebars.AST.IntegerNode=function(b){this.type="INTEGER";this.integer=b};Handlebars.AST.BooleanNode=function(b){this.type="BOOLEAN";this.bool=b};Handlebars.AST.CommentNode=function(b){this.type="comment";this.comment=b}})();
Handlebars.Exception=function(d){var b=Error.prototype.constructor.apply(this,arguments),a;for(a in b)b.hasOwnProperty(a)&&(this[a]=b[a])};Handlebars.Exception.prototype=Error();Handlebars.SafeString=function(d){this.string=d};Handlebars.SafeString.prototype.toString=function(){return this.string.toString()};
(function(){var d={"<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","`":"&#x60;"},b=/&(?!\w+;)|[<>"'`]/g,a=/[&<>"'`]/,g=function(a){return d[a]||"&amp;"};Handlebars.Utils={escapeExpression:function(d){return d instanceof Handlebars.SafeString?d.toString():null==d||!1===d?"":!a.test(d)?d:d.replace(b,g)},isEmpty:function(a){return"undefined"===typeof a?!0:null===a?!0:!1===a?!0:"[object Array]"===Object.prototype.toString.call(a)&&0===a.length?!0:!1}}})();Handlebars.Compiler=function(){};
Handlebars.JavaScriptCompiler=function(){};
(function(d,b){d.OPCODE_MAP={appendContent:1,getContext:2,lookupWithHelpers:3,lookup:4,append:5,invokeMustache:6,appendEscaped:7,pushString:8,truthyOrFallback:9,functionOrFallback:10,invokeProgram:11,invokePartial:12,push:13,assignToHash:15,pushStringParam:16};d.MULTI_PARAM_OPCODES={appendContent:1,getContext:1,lookupWithHelpers:2,lookup:1,invokeMustache:3,pushString:1,truthyOrFallback:1,functionOrFallback:1,invokeProgram:3,invokePartial:1,push:1,assignToHash:1,pushStringParam:1};d.DISASSEMBLE_MAP=
{};for(var a in d.OPCODE_MAP)d.DISASSEMBLE_MAP[d.OPCODE_MAP[a]]=a;d.multiParamSize=function(f){return d.MULTI_PARAM_OPCODES[d.DISASSEMBLE_MAP[f]]};d.prototype={compiler:d,disassemble:function(){for(var f=this.opcodes,c,a=[],b,e=0,g=f.length;e<g;e++)if(c=f[e],"DECLARE"===c)b=f[++e],c=f[++e],a.push("DECLARE "+b+" = "+c);else{b=d.DISASSEMBLE_MAP[c];for(var h=d.multiParamSize(c),i=[],k=0;k<h;k++)c=f[++e],"string"===typeof c&&(c='"'+c.replace("\n","\\n")+'"'),i.push(c);b=b+" "+i.join(" ");a.push(b)}return a.join("\n")},
guid:0,compile:function(f,c){this.children=[];this.depths={list:[]};this.options=c;var a=this.options.knownHelpers;this.options.knownHelpers={helperMissing:!0,blockHelperMissing:!0,each:!0,"if":!0,unless:!0,"with":!0,log:!0};if(a)for(var b in a)this.options.knownHelpers[b]=a[b];return this.program(f)},accept:function(f){return this[f.type](f)},program:function(f){var f=f.statements,a;this.opcodes=[];for(var b=0,d=f.length;b<d;b++)a=f[b],this[a.type](a);this.isSimple=1===d;this.depths.list=this.depths.list.sort(function(f,
a){return f-a});return this},compileProgram:function(f){var f=(new this.compiler).compile(f,this.options),a=this.guid++;this.usePartial=this.usePartial||f.usePartial;this.children[a]=f;for(var b=0,d=f.depths.list.length;b<d;b++)depth=f.depths.list[b],2>depth||this.addDepth(depth-1);return a},block:function(f){var a=f.mustache,b=this.setupStackForMustache(a),d=this.compileProgram(f.program);f.program.inverse&&(f=this.compileProgram(f.program.inverse),this.declare("inverse",f));this.opcode("invokeProgram",
d,b.length,!!a.hash);this.declare("inverse",null);this.opcode("append")},inverse:function(f){var a=this.setupStackForMustache(f.mustache);this.declare("inverse",this.compileProgram(f.program));this.opcode("invokeProgram",null,a.length,!!f.mustache.hash);this.opcode("append")},hash:function(f){var f=f.pairs,a,b;this.opcode("push","{}");for(var d=0,e=f.length;d<e;d++)a=f[d],b=a[1],this.accept(b),this.opcode("assignToHash",a[0])},partial:function(a){var c=a.id;this.usePartial=!0;a.context?this.ID(a.context):
this.opcode("push","depth0");this.opcode("invokePartial",c.original);this.opcode("append")},content:function(a){this.opcode("appendContent",a.string)},mustache:function(a){this.opcode("invokeMustache",this.setupStackForMustache(a).length,a.id.original,!!a.hash);a.escaped?this.opcode("appendEscaped"):this.opcode("append")},ID:function(a){this.addDepth(a.depth);this.opcode("getContext",a.depth);this.opcode("lookupWithHelpers",a.parts[0]||null,a.isScoped||!1);for(var c=1,b=a.parts.length;c<b;c++)this.opcode("lookup",
a.parts[c])},STRING:function(a){this.opcode("pushString",a.string)},INTEGER:function(a){this.opcode("push",a.integer)},BOOLEAN:function(a){this.opcode("push",a.bool)},comment:function(){},pushParams:function(a){for(var c=a.length,b;c--;)if(b=a[c],this.options.stringParams)b.depth&&this.addDepth(b.depth),this.opcode("getContext",b.depth||0),this.opcode("pushStringParam",b.string);else this[b.type](b)},opcode:function(a,c,b,e){this.opcodes.push(d.OPCODE_MAP[a]);void 0!==c&&this.opcodes.push(c);void 0!==
b&&this.opcodes.push(b);void 0!==e&&this.opcodes.push(e)},declare:function(a,c){this.opcodes.push("DECLARE");this.opcodes.push(a);this.opcodes.push(c)},addDepth:function(a){0!==a&&!this.depths[a]&&(this.depths[a]=!0,this.depths.list.push(a))},setupStackForMustache:function(a){var c=a.params;this.pushParams(c);a.hash&&this.hash(a.hash);this.ID(a.id);return c}};b.prototype={nameLookup:function(a,c){return/^[0-9]+$/.test(c)?a+"["+c+"]":b.isValidJavaScriptVariableName(c)?a+"."+c:a+"['"+c+"']"},appendToBuffer:function(a){return this.environment.isSimple?
"return "+a+";":"buffer += "+a+";"},initializeBuffer:function(){return this.quotedString("")},namespace:"Handlebars",compile:function(a,c,b,d){this.environment=a;this.options=c||{};this.name=this.environment.name;this.isChild=!!b;this.context=b||{programs:[],aliases:{self:"this"},registers:{list:[]}};this.preamble();this.stackSlot=0;this.stackVars=[];this.compileChildren(a,c);a=a.opcodes;this.i=0;for(e=a.length;this.i<e;this.i++)a=this.nextOpcode(0),"DECLARE"===a[0]?(this.i+=2,this[a[1]]=a[2]):(this.i+=
a[1].length,this[a[0]].apply(this,a[1]));return this.createFunctionContext(d)},nextOpcode:function(a){var c=this.environment.opcodes,b=c[this.i+a],e,g;if("DECLARE"===b)return e=c[this.i+1],a=c[this.i+2],["DECLARE",e,a];e=d.DISASSEMBLE_MAP[b];b=d.multiParamSize(b);g=[];for(var h=0;h<b;h++)g.push(c[this.i+h+1+a]);return[e,g]},eat:function(a){this.i+=a.length},preamble:function(){var a=[];if(this.isChild)a.push("");else{var c=this.namespace,b="helpers = helpers || "+c+".helpers;";this.environment.usePartial&&
(b=b+" partials = partials || "+c+".partials;");a.push(b)}this.environment.isSimple?a.push(""):a.push(", buffer = "+this.initializeBuffer());this.lastContext=0;this.source=a},createFunctionContext:function(a){var c=this.stackVars;this.isChild||(c=c.concat(this.context.registers.list));0<c.length&&(this.source[1]=this.source[1]+", "+c.join(", "));if(!this.isChild)for(var b in this.context.aliases)this.source[1]=this.source[1]+", "+b+"="+this.context.aliases[b];this.source[1]&&(this.source[1]="var "+
this.source[1].substring(2)+";");this.isChild||(this.source[1]+="\n"+this.context.programs.join("\n")+"\n");this.environment.isSimple||this.source.push("return buffer;");c=this.isChild?["depth0","data"]:["Handlebars","depth0","helpers","partials","data"];b=0;for(var d=this.environment.depths.list.length;b<d;b++)c.push("depth"+this.environment.depths.list[b]);if(a)return c.push(this.source.join("\n  ")),Function.apply(this,c);a="function "+(this.name||"")+"("+c.join(",")+") {\n  "+this.source.join("\n  ")+
"}";Handlebars.log(Handlebars.logger.DEBUG,a+"\n\n");return a},appendContent:function(a){this.source.push(this.appendToBuffer(this.quotedString(a)))},append:function(){var a=this.popStack();this.source.push("if("+a+" || "+a+" === 0) { "+this.appendToBuffer(a)+" }");this.environment.isSimple&&this.source.push("else { "+this.appendToBuffer("''")+" }")},appendEscaped:function(){var a=this.nextOpcode(1),c="";this.context.aliases.escapeExpression="this.escapeExpression";"appendContent"===a[0]&&(c=" + "+
this.quotedString(a[1][0]),this.eat(a));this.source.push(this.appendToBuffer("escapeExpression("+this.popStack()+")"+c))},getContext:function(a){if(this.lastContext!==a)this.lastContext=a},lookupWithHelpers:function(a,c){if(a){var b=this.nextStack();this.usingKnownHelper=!1;!c&&this.options.knownHelpers[a]?(b=b+" = "+this.nameLookup("helpers",a,"helper"),this.usingKnownHelper=!0):b=c||this.options.knownHelpersOnly?b+" = "+this.nameLookup("depth"+this.lastContext,a,"context"):b+" = "+this.nameLookup("helpers",
a,"helper")+" || "+this.nameLookup("depth"+this.lastContext,a,"context");this.source.push(b+";")}else this.pushStack("depth"+this.lastContext)},lookup:function(a){var c=this.topStack();this.source.push(c+" = ("+c+" === null || "+c+" === undefined || "+c+" === false ? "+c+" : "+this.nameLookup(c,a,"context")+");")},pushStringParam:function(a){this.pushStack("depth"+this.lastContext);this.pushString(a)},pushString:function(a){this.pushStack(this.quotedString(a))},push:function(a){this.pushStack(a)},
invokeMustache:function(a,c,b){this.populateParams(a,this.quotedString(c),"{}",null,b,function(a,b,c){if(!this.usingKnownHelper)this.context.aliases.helperMissing="helpers.helperMissing",this.context.aliases.undef="void 0",this.source.push("else if("+c+"=== undef) { "+a+" = helperMissing.call("+b+"); }"),a!==c&&this.source.push("else { "+a+" = "+c+"; }")})},invokeProgram:function(a,c,b){var d=this.programExpression(this.inverse),a=this.programExpression(a);this.populateParams(c,null,a,d,b,function(a,
b){if(!this.usingKnownHelper)this.context.aliases.blockHelperMissing="helpers.blockHelperMissing",this.source.push("else { "+a+" = blockHelperMissing.call("+b+"); }")})},populateParams:function(a,b,d,e,g,h){var m=g||this.options.stringParams||e||this.options.data,i=this.popStack(),k=[];m?(this.register("tmp1",d),d="tmp1"):d="{ hash: {} }";m&&this.source.push("tmp1.hash = "+(g?this.popStack():"{}")+";");this.options.stringParams&&this.source.push("tmp1.contexts = [];");for(m=0;m<a;m++)g=this.popStack(),
k.push(g),this.options.stringParams&&this.source.push("tmp1.contexts.push("+this.popStack()+");");e&&(this.source.push("tmp1.fn = tmp1;"),this.source.push("tmp1.inverse = "+e+";"));this.options.data&&this.source.push("tmp1.data = data;");k.push(d);this.populateCall(k,i,b||i,h)},populateCall:function(a,b,d,e){var g=["depth0"].concat(a).join(", "),a=["depth0"].concat(d).concat(a).join(", "),d=this.nextStack();this.usingKnownHelper?this.source.push(d+" = "+b+".call("+g+");"):(this.context.aliases.functionType=
'"function"',this.source.push("if(typeof "+b+" === functionType) { "+d+" = "+b+".call("+g+"); }"));e.call(this,d,a,b);this.usingKnownHelper=!1},invokePartial:function(a){this.pushStack("self.invokePartial("+this.nameLookup("partials",a,"partial")+", '"+a+"', "+this.popStack()+", helpers, partials);")},assignToHash:function(a){var b=this.popStack();this.source.push(this.topStack()+"['"+a+"'] = "+b+";")},compiler:b,compileChildren:function(a,b){for(var d=a.children,e,g,h=0,m=d.length;h<m;h++){e=d[h];
g=new this.compiler;this.context.programs.push("");var i=this.context.programs.length;e.index=i;e.name="program"+i;this.context.programs[i]=g.compile(e,b,this.context)}},programExpression:function(a){if(null==a)return"self.noop";for(var b=this.environment.children[a],a=b.depths.list,b=[b.index,b.name,"data"],d=0,e=a.length;d<e;d++)depth=a[d],1===depth?b.push("depth0"):b.push("depth"+(depth-1));if(0===a.length)return"self.program("+b.join(", ")+")";b.shift();return"self.programWithDepth("+b.join(", ")+
")"},register:function(a,b){this.useRegister(a);this.source.push(a+" = "+b+";")},useRegister:function(a){this.context.registers[a]||(this.context.registers[a]=!0,this.context.registers.list.push(a))},pushStack:function(a){this.source.push(this.nextStack()+" = "+a+";");return"stack"+this.stackSlot},nextStack:function(){this.stackSlot++;this.stackSlot>this.stackVars.length&&this.stackVars.push("stack"+this.stackSlot);return"stack"+this.stackSlot},popStack:function(){return"stack"+this.stackSlot--},
topStack:function(){return"stack"+this.stackSlot},quotedString:function(a){return'"'+a.replace(/\\/g,"\\\\").replace(/"/g,'\\"').replace(/\n/g,"\\n").replace(/\r/g,"\\r")+'"'}};a="break case catch continue default delete do else finally for function if in instanceof new return switch this throw try typeof var void while with null true false".split(" ");for(var g=b.RESERVED_WORDS={},h=0,e=a.length;h<e;h++)g[a[h]]=!0;b.isValidJavaScriptVariableName=function(a){return!b.RESERVED_WORDS[a]&&/^[a-zA-Z_$][0-9a-zA-Z_$]+$/.test(a)?
!0:!1}})(Handlebars.Compiler,Handlebars.JavaScriptCompiler);Handlebars.precompile=function(d,b){var b=b||{},a=Handlebars.parse(d),a=(new Handlebars.Compiler).compile(a,b);return(new Handlebars.JavaScriptCompiler).compile(a,b)};Handlebars.compile=function(d,b){var b=b||{},a;return function(g,h){if(!a){var e=Handlebars.parse(d),e=(new Handlebars.Compiler).compile(e,b),e=(new Handlebars.JavaScriptCompiler).compile(e,b,void 0,!0);a=Handlebars.template(e)}return a.call(this,g,h)}};
Handlebars.VM={template:function(d){var b={escapeExpression:Handlebars.Utils.escapeExpression,invokePartial:Handlebars.VM.invokePartial,programs:[],program:function(a,b,d){var e=this.programs[a];if(d)return Handlebars.VM.program(b,d);e||(e=this.programs[a]=Handlebars.VM.program(b));return e},programWithDepth:Handlebars.VM.programWithDepth,noop:Handlebars.VM.noop};return function(a,g){g=g||{};return d.call(b,Handlebars,a,g.helpers,g.partials,g.data)}},programWithDepth:function(d,b,a){var g=Array.prototype.slice.call(arguments,
2);return function(a,e){e=e||{};return d.apply(this,[a,e.data||b].concat(g))}},program:function(d,b){return function(a,g){g=g||{};return d(a,g.data||b)}},noop:function(){return""},invokePartial:function(d,b,a,g,h){if(void 0===d)throw new Handlebars.Exception("The partial "+b+" could not be found");if(d instanceof Function)return d(a,{helpers:g,partials:h});if(Handlebars.compile)return h[b]=Handlebars.compile(d),h[b](a,{helpers:g,partials:h});throw new Handlebars.Exception("The partial "+b+" could not be compiled when running in vm mode");
}};Handlebars.template=Handlebars.VM.template;