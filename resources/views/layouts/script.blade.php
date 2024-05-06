<!-- latest jquery-->
<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<!-- Bootstrap js-->
<script src="{{asset('assets/js/bootstrap/bootstrap.bundle.min.js')}}"></script>
<!-- feather icon js-->
<script src="{{asset('assets/js/icons/feather-icon/feather.min.js')}}"></script>
<script src="{{asset('assets/js/icons/feather-icon/feather-icon.js')}}"></script>
@if(auth()->check())
    <!-- scrollbar js-->
    <script src="{{asset('assets/js/scrollbar/simplebar.js')}}"></script>
    <script src="{{asset('assets/js/scrollbar/custom.js')}}"></script>
@endif
<!-- Sidebar jquery-->
<script src="{{asset('assets/js/config.js')}}"></script>
@if(auth()->check())
    <!-- Plugins JS start-->
    <script src="{{asset('assets/js/sidebar-menu.js')}}"></script>
    <script src="{{asset('assets/js/sidebar-pin.js')}}"></script>
    <script src="{{asset('assets/js/slick/slick.min.js')}}"></script>
    <script src="{{asset('assets/js/slick/slick.js')}}"></script>
    <script src="{{asset('assets/js/header-slick.js')}}"></script>
    <script src="{{asset('assets/js/prism/prism.min.js')}}"></script>
    <script src="{{asset('assets/js/clipboard/clipboard.min.js')}}"></script>
    <script src="{{asset('assets/js/custom-card/custom-card.js')}}"></script>
    <script src="{{asset('assets/js/typeahead/handlebars.js')}}"></script>
    <script src="{{asset('assets/js/typeahead/typeahead.bundle.js')}}"></script>
    <script src="{{asset('assets/js/typeahead/typeahead.custom.js')}}"></script>
    <script src="{{asset('assets/js/typeahead-search/handlebars.js')}}"></script>
    <script src="{{asset('assets/js/typeahead-search/typeahead-custom.js')}}"></script>
    <script src="{{asset('assets/js/datatable/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/intl-tel/intlTelInput.min.js')}}"></script>
    <script src="{{asset('assets/select2/select2.min.js')}}"></script>
    <script src="{{asset('assets/sweetalert2@11.js')}}"></script>
    <script src="{{asset('assets/axios.min.js')}}"></script>
    <script src="{{asset('assets/toastr/toastr.min.js')}}"></script>
    <!-- Plugins JS Ends-->
@endif
<!-- Theme js-->
<script src="{{asset('assets/js/script.js')}}"></script>
