<link href="/source/Molajo/Extension/View/Template/Edit/alohaeditor/aloha/css/aloha.css" type="text/css"
	  rel="stylesheet"/>

<script>
	var Aloha = window.Aloha || ( window.Aloha = {} );
	Aloha.settings = {
		locale:'en',
		plugins:{
			format:{
				config:[  'b', 'i', 'p', 'sub', 'sup', 'del', 'title', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'pre', 'removeFormat' ]
			}
		},
		sidebar:{
			disabled:true
		}
	};
</script>

<script type="text/javascript" src="/source/Molajo/Extension/View/Template/Edit/alohaeditor/aloha/lib/aloha.js"
		data-aloha-plugins="common/format,
			                        common/table,
			                        common/list,
			                        common/link,
			                        common/highlighteditables,
			                        common/block,
			                        common/undo,
			                        common/contenthandler,
			                        common/paste,
			                        common/characterpicker,
			                        common/commands,
			                        extra/flag-icons,
			                        common/abbr,
			                        extra/browser,
			                        extra/linkbrowser"></script>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

<script type="text/javascript">
	Aloha.ready(function () {
		Aloha.require(['aloha', 'aloha/jquery'], function (Aloha, $) {
			$('#JustEdit').aloha();
		});
	});
</script>
