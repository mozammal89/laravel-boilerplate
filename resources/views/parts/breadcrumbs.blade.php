@if($title != null && count($menu_items) != 0)
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>{{$title}}</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{route('/')}}">
                                <svg class="stroke-icon">
                                    <use href="{{asset('assets/svg/icon-sprite.svg')}}#stroke-home"></use>
                                </svg>
                            </a>
                        </li>
                        @foreach($menu_items as $item)
                            <li class="breadcrumb-item">{{$item}}</li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endif
