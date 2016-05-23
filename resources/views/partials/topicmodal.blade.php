<div class="large reveal" id="animatedModal10" data-reveal data-close-on-click="true" data-animation-in="fade-in" data-animation-out="fade-out">
 	
 	<h5>Create a New Topic of the day</h5>

	 <form action="{{ route('create_topic') }}" method="POST" enctype="multipart/form-data">
	 <input type="hidden" id="token" name="_token" value="<?php echo csrf_token(); ?>">
	  <div class="row">
		  <div class="small-12 medium-5 large-5 columns">
		  	<div class="row">
		  		<div class="small-12 medium-12 large-12 columns">
			    	<label>Choose Topic
			    		<select name="topic_title_id">
			    			<option value="">Choose one</option>
			    			@foreach($titles as $title)
			    			<option value="{{ $title->id }}">{{ $title->topic_title }}</option>
			    			@endforeach
			    		</select>
			    	</label>
			    </div>


		  		<div class="small-12 medium-12 large-12 columns">
			     <label>Title
			        <input type="text" name="title" id="title" placeholder="write something" />
			      </label>
			    </div>
			   
			    <div class="small-12 medium-12 large-12 columns">
			    	<input type="hidden" value="" id="tagValue" />
			    	<label>Tags
			    		<select id="selective" name="tags[]" multiple="" class="ui fluid search dropdown">
			    		@foreach($tags as $tag)
			    		<option value="{{ $tag->name }}">#{{ $tag->name }}</option>
			    		@endforeach
						</select>
			    	</label>
			    </div>
			    <input type="hidden" name="slug" id="slug" value="" />
			    <div class="small-12 medium-12 large-12 columns">
			      	<input type="hidden" name="MAX_FILE_SIZE" value="52428800" /> 
			        <label for="pictureUpload" class="expanded button">Upload a picture</label>
					<input name="file" type="file" id="pictureUpload" class="show-for-sr">
			    </div>
			    <div class="small-12 medium-12 large-12 columns">
			      	<label for="videoUpload" class="expanded button" >Upload a Video</label>
			      	<input type="hidden" name="MAX_FILE_SIZE" value="52428800" /> 
			        <input id="videoUpload" type="file" name="video" class="show-for-sr"/>
			    </div>

		  	</div>
		  </div>
		   <div class="small-12 medium-7 large-7 columns">
		  		<div class="small-12 medium-12 large-12 columns">
			      <label>Body
			        <textarea name="body" id="body" placeholder="write something"></textarea>
			      </label>
			    </div>
		  </div>
	  </div>





	  <div style="padding-left: 10px; padding-right: 10px;">
	  	<input type="submit" value="submit" class="expanded button" id="submit" />
	  </div>
	</form>



  <button class="close-button" data-close aria-label="Close reveal" type="button">
    <span aria-hidden="true">&times;</span>
  </button>
</div>