<?php

//Import & Export
Route::group(['prefix'=>"import-export", 'namespace'=>"ImportExportControllers"], function() {
	

	Route::get('/', ['uses'=>"IEController@getIndex", "as"=>"import-export-list"]);

	//IE Country
	Route::get('import-country', ['uses'=>"IECountryController@getImport", "as"=>"import-country"]);
	Route::post('import-country', ['uses'=>"IECountryController@postImport", "as"=>"import-country-post"]);
	Route::get('export-country', ['uses'=>"IECountryController@getExport", "as"=>"export-country"]);
	Route::post('export-country', ['uses'=>"IECountryController@postExport", "as"=>"export-country-post"]);
	Route::get('country-sample', ['uses'=>"IECountryController@getSample", "as"=>"import-country-sample"]);

	//IE State
	Route::get('import-state', ['uses'=>"IEStateController@getImport", "as"=>"import-state"]);
	Route::post('import-state', ['uses'=>"IEStateController@postImport", "as"=>"import-state-post"]);
	Route::get('export-state', ['uses'=>"IEStateController@getExport", "as"=>"export-state"]);
	Route::post('export-state', ['uses'=>"IEStateController@postExport", "as"=>"export-state-post"]);
	Route::get('state-sample', ['uses'=>"IEStateController@getSample", "as"=>"import-state-sample"]);

	//IE State
	Route::get('import-city', ['uses'=>"IECityController@getImport", "as"=>"import-city"]);
	Route::post('import-city', ['uses'=>"IECityController@postImport", "as"=>"import-city-post"]);
	Route::get('export-city', ['uses'=>"IECityController@getExport", "as"=>"export-city"]);
	Route::post('export-city', ['uses'=>"IECityController@postExport", "as"=>"export-city-post"]);
	Route::get('city-sample', ['uses'=>"IECityController@getSample", "as"=>"import-city-sample"]);


	//IE Job Category
	Route::get('import-jobcategory', ['uses'=>"IEJobCategoryController@getImport", "as"=>"import-jobcategory"]);
	Route::post('import-jobcategory', ['uses'=>"IEJobCategoryController@postImport", "as"=>"import-jobcategory-post"]);
	Route::get('export-jobcategory', ['uses'=>"IEJobCategoryController@getExport", "as"=>"export-jobcategory"]);
	Route::post('export-jobcategory', ['uses'=>"IEJobCategoryController@postExport", "as"=>"export-jobcategory-post"]);
	Route::get('jobcategory-sample', ['uses'=>"IEJobCategoryController@getSample", "as"=>"import-jobcategory-sample"]);

	//IE Job Title
	Route::get('import-jobtitle', ['uses'=>"IEJobTitleController@getImport", "as"=>"import-jobtitle"]);
	Route::post('import-jobtitle', ['uses'=>"IEJobTitleController@postImport", "as"=>"import-jobtitle-post"]);
	Route::get('export-jobtitle', ['uses'=>"IEJobTitleController@getExport", "as"=>"export-jobtitle"]);
	Route::post('export-jobtitle', ['uses'=>"IEJobTitleController@postExport", "as"=>"export-jobtitle-post"]);
	Route::get('jobtitle-sample', ['uses'=>"IEJobTitleController@getSample", "as"=>"import-jobtitle-sample"]);

	//IE Salary Type
	Route::get('import-salary-type', ['uses'=>"IESalaryTypeController@getImport", "as"=>"import-salary-type"]);
	Route::post('import-salary-type', ['uses'=>"IESalaryTypeController@postImport", "as"=>"import-salary-type-post"]);
	Route::get('export-salary-type', ['uses'=>"IESalaryTypeController@getExport", "as"=>"export-salary-type"]);
	Route::post('export-salary-type', ['uses'=>"IESalaryTypeController@postExport", "as"=>"export-salary-type-post"]);
	Route::get('salary-type-sample', ['uses'=>"IESalaryTypeController@getSample", "as"=>"import-salary-type-sample"]);

	//IE Experience
	Route::get('import-experience', ['uses'=>"IEExperienceController@getImport", "as"=>"import-experience"]);
	Route::post('import-experience', ['uses'=>"IEExperienceController@postImport", "as"=>"import-experience-post"]);
	Route::get('export-experience', ['uses'=>"IEExperienceController@getExport", "as"=>"export-experience"]);
	Route::post('export-experience', ['uses'=>"IEExperienceController@postExport", "as"=>"export-experience-post"]);
	Route::get('experience-sample', ['uses'=>"IEExperienceController@getSample", "as"=>"import-experience-sample"]);

	//IE Experience Level
	Route::get('import-experience-level', ['uses'=>"IEExperienceLevelController@getImport", "as"=>"import-experience-level"]);
	Route::post('import-experience-level', ['uses'=>"IEExperienceLevelController@postImport", "as"=>"import-experience-level-post"]);
	Route::get('export-experience-level', ['uses'=>"IEExperienceLevelController@getExport", "as"=>"export-experience-level"]);
	Route::post('export-experience-level', ['uses'=>"IEExperienceLevelController@postExport", "as"=>"export-experience-level-post"]);
	Route::get('experience-level-sample', ['uses'=>"IEExperienceLevelController@getSample", "as"=>"import-experience-level-sample"]);

	//IE Job Type
	Route::get('import-job-type', ['uses'=>"IEJobTypeController@getImport", "as"=>"import-job-type"]);
	Route::post('import-job-type', ['uses'=>"IEJobTypeController@postImport", "as"=>"import-job-type-post"]);
	Route::get('export-job-type', ['uses'=>"IEJobTypeController@getExport", "as"=>"export-job-type"]);
	Route::post('export-job-type', ['uses'=>"IEJobTypeController@postExport", "as"=>"export-job-type-post"]);
	Route::get('job-type-sample', ['uses'=>"IEJobTypeController@getSample", "as"=>"import-job-type-sample"]);

	//IE Industry
	Route::get('import-industry', ['uses'=>"IEIndustryController@getImport", "as"=>"import-industry"]);
	Route::post('import-industry', ['uses'=>"IEIndustryController@postImport", "as"=>"import-industry-post"]);
	Route::get('export-industry', ['uses'=>"IEIndustryController@getExport", "as"=>"export-industry"]);
	Route::post('export-industry', ['uses'=>"IEIndustryController@postExport", "as"=>"export-industry-post"]);
	Route::get('industry-sample', ['uses'=>"IEIndustryController@getSample", "as"=>"import-industry-sample"]);

	//IE Education
	Route::get('import-education', ['uses'=>"IEEducationController@getImport", "as"=>"import-education"]);
	Route::post('import-education', ['uses'=>"IEEducationController@postImport", "as"=>"import-education-post"]);
	Route::get('export-education', ['uses'=>"IEEducationController@getExport", "as"=>"export-education"]);
	Route::post('export-education', ['uses'=>"IEEducationController@postExport", "as"=>"export-education-post"]);
	Route::get('education-sample', ['uses'=>"IEEducationController@getSample", "as"=>"import-education-sample"]);

	//IE Degree
	Route::get('import-degree', ['uses'=>"IEDegreeController@getImport", "as"=>"import-degree"]);
	Route::post('import-degree', ['uses'=>"IEDegreeController@postImport", "as"=>"import-degree-post"]);
	Route::get('export-degree', ['uses'=>"IEDegreeController@getExport", "as"=>"export-degree"]);
	Route::post('export-degree', ['uses'=>"IEDegreeController@postExport", "as"=>"export-degree-post"]);
	Route::get('degree-sample', ['uses'=>"IEDegreeController@getSample", "as"=>"import-degree-sample"]);

	//IE Tag
	Route::get('import-tag', ['uses'=>"IETagController@getImport", "as"=>"import-tag"]);
	Route::post('import-tag', ['uses'=>"IETagController@postImport", "as"=>"import-tag-post"]);
	Route::get('export-tag', ['uses'=>"IETagController@getExport", "as"=>"export-tag"]);
	Route::post('export-tag', ['uses'=>"IETagController@postExport", "as"=>"export-tag-post"]);
	Route::get('tag-sample', ['uses'=>"IETagController@getSample", "as"=>"import-tag-sample"]);

	//IE Salary Range
	Route::get('import-salary-range', ['uses'=>"IESalaryRangeController@getImport", "as"=>"import-salary-range"]);
	Route::post('import-salary-range', ['uses'=>"IESalaryRangeController@postImport", "as"=>"import-salary-range-post"]);
	Route::get('export-salary-range', ['uses'=>"IESalaryRangeController@getExport", "as"=>"export-salary-range"]);
	Route::post('export-salary-range', ['uses'=>"IESalaryRangeController@postExport", "as"=>"export-salary-range-post"]);
	Route::get('salary-range-sample', ['uses'=>"IESalaryRangeController@getSample", "as"=>"import-salary-range-sample"]);

	//IE Recruiter 
	Route::get('import-recruitertype', ['uses'=>"IERecruitertypeController@getImport", "as"=>"import-recruitertype"]);
	Route::post('import-recruitertype', ['uses'=>"IERecruitertypeController@postImport", "as"=>"import-recruitertype-post"]);
	Route::get('export-recruitertype', ['uses'=>"IERecruitertypeController@getExport", "as"=>"export-recruitertype"]);
	Route::post('export-recruitertype', ['uses'=>"IERecruitertypeController@postExport", "as"=>"export-recruitertype-post"]);
	Route::get('salary-recruitertype', ['uses'=>"IERecruitertypeController@getSample", "as"=>"import-recruitertype-sample"]);

	//IE Job
	Route::get('import-job', ['uses'=>"IEJobController@getImport", "as"=>"import-job"]);
	Route::post('import-job', ['uses'=>"IEJobController@postImport", "as"=>"import-job-post"]);
	Route::get('export-job', ['uses'=>"IEJobController@getExport", "as"=>"export-job"]);
	Route::post('export-job', ['uses'=>"IEJobController@postExport", "as"=>"export-job-post"]);
	Route::get('salary-job', ['uses'=>"IEJobController@getSample", "as"=>"import-job-sample"]);

	//IE employer
	Route::get('import-employer', ['uses'=>"IEEmployerController@getImport", "as"=>"import-employer"]);
	Route::post('import-employer', ['uses'=>"IEEmployerController@postImport", "as"=>"import-employer-post"]);
	Route::get('export-employer', ['uses'=>"IEEmployerController@getExport", "as"=>"export-employer"]);
	Route::post('export-employer', ['uses'=>"IEEmployerController@postExport", "as"=>"export-employer-post"]);
	Route::get('employer-sample', ['uses'=>"IEEmployerController@getSample", "as"=>"import-employer-sample"]);

	//IE Jobseeker
	Route::get('import-jobseeker', ['uses'=>"IEJobseekerController@getImport", "as"=>"import-jobseeker"]);
	Route::post('import-jobseeker', ['uses'=>"IEJobseekerController@postImport", "as"=>"import-jobseeker-post"]);
	Route::get('export-jobseeker', ['uses'=>"IEJobseekerController@getExport", "as"=>"export-jobseeker"]);
	Route::post('export-jobseeker', ['uses'=>"IEJobseekerController@postExport", "as"=>"export-jobseeker-post"]);
	Route::get('jobseeker-sample', ['uses'=>"IEJobseekerController@getSample", "as"=>"import-jobseeker-sample"]);
});