<div class="modal" tabindex="-1" role="dialog" id="mainModal">
	<div class="modal-dialog">
		<div class="modal-content loader">
			<div class="modal-body text-center">
				<p><img src="{{asset('imgs/loader.gif')}}" /><br/><br/><label>Loading Calendar</label></p>
			</div>
		</div>
		<div class="modal-content calendar hide">
			<div class="modal-body">
				<div id="calendar"></div>
			</div>
			<div class="modal-footer">
				<div class="pull-left">
					<label class="label label-primary">Selected Day</label>
					<label class="label label-warning">Job Day</label>
				</div>
				<div class="pull-right">
					<form action="">
						<select name="days[]" class="hide" multiple></select>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary">Apply Now</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>