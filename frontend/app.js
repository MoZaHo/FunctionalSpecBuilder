var docid = 1;

angular.module('project', [])
 
.controller('general', function($scope , DataService) {

	$scope.chapters = [];
	
	$scope.tmpchapters = {};
	
	$scope.article = {};
	$scope.article_parts = {};
	
	$scope.edit = true;
	
	$scope.newchapter = "";
	$scope.selectedparent = 0;
	
	$scope.ActiveChapter = {};
	$scope.ActiveIndex = 0;
	
	$scope.indexed = [];
	
	$scope.GetChapters = function(parent_id) {
		//This is only for the MAIN 
		
		DataService.get('chapter',{'parent_id' : parent_id, 'document_id' : docid, 'sort':'order', 'limit' : 1000}).then(function(d) {
			$scope.chapters = d;
			$scope.GetArticle(parent_id);
		});
	};
	
	$scope.GetSubChapters = function(chapter) {
		
		
		if (typeof chapter === 'object') {
			$scope.ActiveChapter = chapter;
			
			id = chapter.id;
			//$scope.BreadCrumb(chapter);
			
			$scope.GetArticle(id);
			
			DataService.get('chapter',{'parent_id' : id , 'document_id' : docid, 'sort':'order'}).then(function(d) {
				$scope.chapters = d;
				
			});
			
		} else {
			id = chapter;
			if (id == 0) {
				
			}
			
			DataService.get('chapter',{'parent_id' : id , 'document_id' : docid, 'sort':'order'}).then(function(d) {
				$scope.chapters = d;
				
				if (id > 0) {
					DataService.get('chapter',{'id' : id , 'document_id' : docid, 'sort':'order'}).then(function(d) {
						$scope.ActiveChapter = d;
						$scope.GetArticle($scope.ActiveChapter.id);
					});
				} else {
					$scope.ActiveChapter = {};
				}
			});
		}
		
	};
	
	$scope.breadcrumb = [];
	$scope.BreadCrumb = function(chapter) {
		//loop through existing breadcrumb
		var found = false;
		counter = 0;
		angular.forEach($scope.breadcrumb,function(v,k) {
			if (v.id == chapter.id) {
				found = true;
			}
			
			if (found) {
				if (counter > 0) {
					delete $scope.breadcrumb[k];
					$scope.breadcrumb.splice(k, 1);
				}
				counter++;
			}
		});
		
		if (!found) {
			$scope.breadcrumb.push(chapter);
		}
	}
	
	$scope.GetChapters(0);
	
	$scope.CreateChapter = function(parent_id) {
		if ($scope.ActiveChapter.id == undefined) {
			$scope.ActiveChapter.id = 0;
		}
		DataService.post('chapter',{'parent_id' : $scope.ActiveChapter.id, 'title' : $scope.newchapter, 'document_id' : docid}).then(function(d) {
			$scope.GetChapters(0);
			$scope.newchapter = "";
		});
	}
	
	$scope.GetArticle = function(chapter) {
		if (chapter > 0) {
			options = { 'chapter_id' : chapter };
			
			DataService.get('article',options).then(function(d) {
				if (d[0] != undefined) {
					$scope.article = d;
					DataService.get('article_part',{'article_id' : d[0].id}).then(function(ad) {
						$scope.article_parts = ad
						
						angular.forEach($scope.article_parts , function(v,k) {
							if(v.type == 'tbl') {
								$scope.article_parts[k].data = JSON.parse(v.data);
							}
							
							if(v.type == 'dtbl') {
								$scope.article_parts[k].data = JSON.parse(v.data);
							}
							
							if(v.type == 'ctbl') {
								$scope.article_parts[k].data = JSON.parse(v.data);
							}
						});
						
					});
				} else {
					console.log("NO ARTICLE! CREATE ONE!" , chapter);
					DataService.post('article',{'chapter_id' : chapter, 'title' : 'Not Set'}).then(function(d) {
						$scope.GetArticle(chapter);
					});
				}
			});
		}
	}
	
	$scope.AddPart = function(type) {
		d = '';
		if (type == 'tbl') {
			d = {};
		}
		
		if (type == 'dtbl') {
			d = {};
		}
		
		if (type == 'ctbl') {
			d = {};
		}
		
		tmp = {'type' : type , 'data' : d};
		$scope.article_parts.push(tmp);
	};
	
	$scope.AddRow = function(o) {
		tmp = {'field':'','type':'','notes':'','criteria':'','link':''};
		$scope.article_parts[o].data[Object.keys($scope.article_parts[o].data).length] = tmp;
	};
	
	$scope.AddCustomRow = function(o) {
		tmp = {};
		$scope.article_parts[o].data[Object.keys($scope.article_parts[o].data).length] = tmp;
	}
	
	$scope.Save = function() {
		angular.forEach($scope.article_parts , function(v,k) {
			if (v.type == "tbl") {
				dd = JSON.stringify(v.data);
			} else if (v.type == "dtbl") {
				dd = JSON.stringify(v.data);
			} else if (v.type == "ctbl") {
				dd = JSON.stringify(v.data);
			} else {
				dd = v.data;
			}
			
			options = {
				type : v.type,
				data : dd,
				article_id : $scope.article[0].id
			};
			
			if (v.article_id != undefined) {
				DataService.put('article_part/' + v.id ,options);
			} else {
				console.log("Create new record");
				DataService.post('article_part',options);
			}
		});
		//DataService.post('article_part',$scope.article_parts);
	};
	
	$scope.BuildPdf = function() {
		
	};
	
})
 
.service('DataService',function($http) {
	
	var DataService = {
	    get: function(service,options) {
	      // $http returns a promise, which has a then function, which also returns a promise
	      var promise = $http({
	    	  url : 'http://localhost:1337/' + service , 
	    	  method : 'GET',
	    	  params : options
	    	  }).then(function (response) {
	    	  	
	        return response.data;
	      });
	      return promise;
	    },
		
	    post : function(service , options) {
			var promise = $http({
		    	  url : 'http://localhost:1337/' + service + '/create?' , 
		    	  method : 'POST',
		    	  params : options
		    	  }).then(function (response) {
		    	  	
		        return response.data;
		      });
		      return promise;
		},
	    
	    put : function(service , options) {
			var promise = $http({
		    	  url : 'http://localhost:1337/' + service, 
		    	  method : 'PUT',
		    	  params : options
		    	  }).then(function (response) {
		    	  	
		        return response.data;
		      });
		      return promise;
		}
	
	  };
	  return DataService;
	
	
});