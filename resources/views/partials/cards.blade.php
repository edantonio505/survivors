<div class="grid-item">

  <div class="card">
    <img class="card_image" src="{{ $topic->user->getAvatarListUrl() }}" alt="">
    <a href="{{ route('profile', ['username' => $topic->user->name]) }}">{{ $topic->user->name }}</a>
    @if($topic->checkIfHasPhotos())
    <div class="image">
      @foreach($topic->photos as $photo)
      <img src="{{ $photo->thumbnail_path }}">
      @endforeach
      <span class="title">{{ $topic->title }}</span>
      <span style="font-size: 12px; margin-left: 10px;">{{ $topic->getTitle() }}</span>
    </div>
    @elseif($topic->checkIfHasVideo())
    <div class="video">
      <video width="560" height="315" controls>
        <source src="{{ $topic->video }}" type="video/mp4">
      Your browser does not support the video tag.
      </video>
    
    <span class="title">{{ $topic->title }}</span>
    <span style="font-size: 12px; margin-left: 10px;">{{ $topic->getTitle() }}</span>
    </div>
    @else

    <h4 style="color:grey; margin-left:20px; margin-top: 10px;">{{ $topic->title }}</h4>

    @endif

    <div class="content">
      <p>{{ $topic->summary() }}...</p>
      <p style="font-size: 12px; color: #bdbdbd;">{{ $topic->commentsCount() }}</p>
      <p style="font-size: 12px; color: #bdbdbd;">{{ $topic->created_at->diffForHumans() }}</p>
      <p style="font-size: 12px; color: #bdbdbd;">{{ $topic->inspiredCount() }}</p>
      <p style="font-size: 12px; color: #bdbdbd;">
      @foreach($topic->tags as $tag)
        #{{ $tag->name }} 
      @endforeach</p>
    </div>
    <span style="margin-left: 20px;">
    @if(Auth::check())
      @if(Auth::user()->checkInspirationTopic($topic))
        <a class="link uninspire" data-id="{{ $topic->id }}"
         href="{{ route('uninspiresUser', ['id' => $topic->id]) }}" 
        >Inspired</a>
      @else 
        <a class="link inspire" data-id="{{ $topic->id }}"
         href="{{ route('inspiresUser', ['id' => $topic->id]) }}"
        >This inpires me</a>
      @endif
    @endif
    </span>
    <div class="action">
      <a href='{{ route('topicPage', ['slug' => $topic->slug, 'by' => $topic->user->name]) }}'>Show more</a>
    </div>
  </div>
</div>