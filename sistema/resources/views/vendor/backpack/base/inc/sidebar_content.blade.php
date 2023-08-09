{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}


<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i
            class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>


<?php
$user = backpack_user();
?>
    <!-- Users, Roles, Permissions -->
@if($user->isAdmin())
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i>@lng("Users")</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item">
                <a class="nav-link" href="{{ backpack_url('user') }}"><i
                        class="nav-icon la la-user"></i><span>@lng("Listar Todos")</span>
                </a>
            </li>
            @foreach(\App\Enums\UserType::getValues() as $userType)
                <li class="nav-item">
                    <a class="nav-link" href='{{ backpack_url('user') }}?type=["<?=$userType?>"]'><i
                            class="nav-icon la la-user"></i><span>@lng($userType)</span>
                    </a>
                </li>
            @endforeach

        </ul>
    </li>
@endif
<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('humor-site') }}">
        <i class="nav-icon las la-grin-squint-tears"></i> @lng("Humor sites")
    </a>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-search"></i>@lng("Searchs")</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('search-with-object') }}"><i
                    class="nav-icon la la-search"></i><span>@lng("Searchs")</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('image-search') }}"><i
                    class="nav-icon la la-photo-video"></i><span>@lng("Images")</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('news') }}"><i
                    class="nav-icon la la-newspaper"></i><span>@lng("News")</span>
            </a>
        </li>

    </ul>
</li>
<li class="nav-item"><a class="nav-link text-capitalize" href="{{ backpack_url('review') }}"><i
            class="nav-icon la la-check-double"></i> @lng("reviews")</a></li>
<li class="nav-item"><a class="nav-link text-capitalize" href="{{ backpack_url('review-source') }}"><i
            class="nav-icon la la-link"></i> @lng("review sources")</a></li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i>@lng("Api")</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('api.index')  }}"><i
                    class="nav-icon la la-plug"></i><span>@lng("Apis")</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-capitalize" href="{{ route('api-request.index') }}">
                <i class="nav-icon la la-list"></i> <span>@lng("requests")</span></a>
        </li>

    </ul>
</li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('domain') }}"><i class="nav-icon la la-sitemap"></i>
        @lng("Domains")</a></li>

@if($user->isAdmin())
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('blocked-ip') }}"><i
                class="nav-icon la la-ban"></i>@lng("Blocked ips")</a></li>

@endif
@if($user->isSuperUser())
    <li class="nav-item"><a class="nav-link" href="{{url('admin/telescope')}}"><i class="nav-icon la la-bug"></i>Telescope</a>
    </li>
    <li class="nav-item"><a class="nav-link" href="{{url('admin/horizon')}}"><i
                class="nav-icon la la-bug"></i>Horizon</a>
    </li>
@endif


@push('before_scripts')
    @livewireScripts
    @include("vendor.backpack.base.inc.before_scripts")
@endpush
@section('before_styles')
    @livewireStyles
@endsection

@push('after_scripts')
    <script>
        $(document).ajaxStart(function () {
            console.log(Livewire)
            if (typeof Livewire.emit === 'function') {
                Livewire.emit('ajaxUpdatedSearch', window.location.search)
            }

        });
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    </script>
@endpush



