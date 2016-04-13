<!doctype html>
<html ng-app="project">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>AppyPet</title>
<script
	src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular.min.js"></script>

<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
	integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"
	crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script
	src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
	integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
	crossorigin="anonymous"></script>

<link href="styles.css" rel="stylesheet">

<script type="text/javascript" src="app.js"></script>

</head>

<body>

	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed"
					data-toggle="collapse" data-target="#navbar" aria-expanded="false"
					aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span> <span
						class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">AppyPet v2</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="#">Dashboard</a></li>
					<li><a href="#">Settings</a></li>
					<li><a href="#">Profile</a></li>
					<li><a href="#">Help</a></li>
				</ul>
				<form class="navbar-form navbar-right">
					<input type="text" class="form-control" placeholder="Search...">
				</form>
			</div>
		</div>
	</nav>

	<div class="container-fluid" ng-controller="general">
		<div class="row">
			<div class="col-md-2 sidebar">
			
				<div class="panel panel-default">
					
					<div class="panel-heading" role="tab">
						<h4 class="panel-title">
							Chapters
						</h4>
					</div>
					<div aria-labelledby="h1" role="tabpanel">
						<div class="panel-body">
							<ul class="sortable">
								<li ng-if="ActiveChapter"><a href="" ng-click="GetSubChapters(ActiveChapter.parent_id)">Back</a></li>
								<li ng-repeat="ss in chapters"><a href="" ng-click="GetSubChapters(ss)">{{ ss.title }} ({{ ss.id }})</a></li>
							</ul>
						</div>
						
					</div>
					
				</div>
				
				
				<div class="form-group">
    				<label for="NewChapter">Chapter (pid : {{ ActiveChapter.id }})</label>
    				<input type="text" class="form-control" id="NewChapter" placeholder="New Chapter" ng-model="newchapter">
    				
  				</div>
  				<button class="btn btn-block btn-primary" ng-click="CreateChapter()">Create Chapter</button>
  				<button class="btn btn-block btn-danger" ng-click="BuildPdf()">Build PDF</button>
				
			</div>
		
			<div class="col-md-8 col-md-offset-2 main" >
				<h3>{{ ActiveChapter.title }}</h3>
				<div ng-repeat="ap in article_parts">
				
					<div ng-if="ap.type == 'str'">
						<textarea ng-if="edit" class="form-control" rows="10" ng-model="article_parts[$index].data">{{ ap.data }}</textarea>
						<div ng-if="!edit">{{ ap.data }}</div>
					</div>
					
					<div class="text-center" ng-if="ap.type == 'img'" >
						<img ng-src="assets/appypetv2/images/{{article_parts[$index].data}}" ng width="350px"/><br/>
						<input type="text" ng-model="article_parts[$index].data" ng-if="edit">
						<br/><br/>
					</div>
					
					<div ng-if="ap.type == 'ctbl'">
						<input type="text" placeholder="Comma seperated table headers" class="form-control" ng-model="ap.data[0]" ng-if="edit"><br/>
						<table class="table table-rounded table-bordered table-striped">
							<thead>
								<tr>
									<th ng-repeat="(thk,thv) in ap.data[0].split(',')">{{thv}}</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="td in ap.data" ng-if="$index > 0">
									<td ng-repeat="(thk,thv) in ap.data[0].split(',')">
										<input type="text" class="form-control" ng-model="td[$index]" ng-if="edit">
										<span ng-if="!edit">{{ td[$index] }}</span>
									</td>
								</tr>
							</tbody>
							<tfoot ng-if="edit">
								<tr>
									<td colspan="5">
										<button class="btn btn-xs btn-primary" ng-click="AddCustomRow($index)">Add Row</button>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
					
					<table class="table table-rounded table-bordered table-striped" ng-if="ap.type == 'dtbl'">
						<thead>
							<tr>
								<th>
									Field
								</th>
								<th>
									Type
								</th>
								<th>
									Notes
								</th>
								<th>
									Ref
								</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="td in ap.data">
								<td>
									<input type="text" class="form-control" ng-model="td.field" ng-if="edit">
									<span ng-if="!edit">{{ td.field }}</span>
								</td>
								<td>
									<input type="text" class="form-control" ng-model="td.type" ng-if="edit">
									<span ng-if="!edit">{{ td.type }}</span>
								</td>
								<td>
									<input type="text" class="form-control" ng-model="td.notes" ng-if="edit">
									<span ng-if="!edit">{{ td.notes }}</span>
								</td>
								<td>
									<input type="text" class="form-control" ng-model="td.link" ng-if="edit">
									<a ng-if="!edit" href="" ng-click="GetSubChapters(td.link.split('|')[0])">{{ td.link.split('|')[1] }}</a>
								</td>
							</tr>
						</tbody>
						<tfoot ng-if="edit">
							<tr>
								<td colspan="5">
									<button class="btn btn-xs btn-primary" ng-click="AddRow($index)">Add Row</button>
								</td>
							</tr>
						</tfoot>
					</table>
					<br/><br/>
					
					
					
					<table class="table table-rounded table-bordered table-striped" ng-if="ap.type == 'tbl'">
						<thead>
							<tr>
								<th>
									Field
								</th>
								<th>
									Type
								</th>
								<th>
									Notes
								</th>
								<th>
									Criteria
								</th>
								<th>
									Link
								</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="td in ap.data">
								<td>
									<input type="text" class="form-control" ng-model="td.field" ng-if="edit">
									<span ng-if="!edit">{{ td.field }}</span>
								</td>
								<td>
									<input type="text" class="form-control" ng-model="td.type" ng-if="edit">
									<span ng-if="!edit">{{ td.type }}</span>
								</td>
								<td>
									<input type="text" class="form-control" ng-model="td.notes" ng-if="edit">
									<span ng-if="!edit">{{ td.notes }}</span>
								</td>
								<td>
									<input type="text" class="form-control" ng-model="td.criteria" ng-if="edit">
									<span ng-if="!edit">{{ td.criteria }}</span>
								</td>
								<td>
									<input type="text" class="form-control" ng-model="td.link" ng-if="edit">
									<a ng-if="!edit" href="" ng-click="GetSubChapters(td.link.split('|')[0])">{{ td.link.split('|')[1] }}</a>
								</td>
							</tr>
						</tbody>
						<tfoot ng-if="edit">
							<tr>
								<td colspan="5">
									<button class="btn btn-xs btn-primary" ng-click="AddRow($index)">Add Row</button>
								</td>
							</tr>
						</tfoot>
					</table>
					<br/><br/>
				</div> 
				
				<div class="well" ng-if="edit">
					<h4>Add Part</h4>
					
					<div class="btn-group btn-group-justified" role="group" aria-label="...">
						<div class="btn-group" role="group">
							<button class="btn btn-default" ng-click="AddPart('str')">Add Text</button>
						</div>
						<div class="btn-group" role="group">
							<button class="btn btn-default" ng-click="AddPart('img')">Add Image</button>
						</div>
						<div class="btn-group" role="group">
							<button class="btn btn-default" ng-click="AddPart('tbl')">Add Table</button>
						</div>
						<div class="btn-group" role="group">
							<button class="btn btn-default" ng-click="AddPart('dtbl')">Add Database Table</button>
						</div>
						<div class="btn-group" role="group">
							<button class="btn btn-default" ng-click="AddPart('ctbl')">Add Custom Table</button>
						</div>
					</div>
				</div>
				<div ng-if="!edit"><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></div>
				<button class="btn btn-primary" ng-click="edit = !edit">Preview</button>
				<button class="btn btn-primary" ng-if="article.length" ng-click="Save()">Save</button>
				<button class="btn btn-danger" ng-if="article.length" ng-click="Save()">Delete Chapter</button>
			</div>
			
			<div class="col-md-2 main well" ng-if="edit">
				<h4>Active Chapter</h4>
				{{ ActiveChapter }}
				<h4>Debug</h4>
				{{ article_parts }} 
				<hr>
				{{ tmpchapters }}
			</div>
		</div>
	</div>

</body>
</html>