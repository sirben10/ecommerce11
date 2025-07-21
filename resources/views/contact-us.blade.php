@extends('layouts.app')
@section('content')
<style>
    .text-danger {
    color: #b04037 !important;
}
</style>
      <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="contact-us container">
      <div class="mw-930">
        <h2 class="page-title">CONTACT US</h2>
      </div>
    </section>

    <hr class="mt-2 text-secondary " />
    <div class="mb-4 pb-4"></div>

    <section class="contact-us container">
      <div class="mw-930">
        <div class="contact-us__form">
            @if (Session::has('success'))
                <p class="alert alert-success">{{ Session::get('success') }}</p>
            @endif
          <form action="{{ route('home.contact.store') }}" name="contact-us-form" class="needs-validation" novalidate="" method="POST">
            @csrf
            <h3 class="mb-5">Get In Touch</h3>
            <div class="form-floating my-4">
              <input type="text" class="form-control" value="{{ old('name') }}" name="name" placeholder="Name *" required="">
              <label for="contact_us_name">Name *</label>
              <span class="text-danger"></span>
            </div>
             @error('name')
                        <span class="text-danger text-center">{{ $message }}</span>
                    @enderror
            <div class="form-floating my-4">
              <input type="text" class="form-control" value="{{ old('phone') }}" name="phone" placeholder="Phone *" required="">
              <label for="contact_us_name">Phone *</label>
              <span class="text-danger"></span>
            </div>
             @error('phone')
                        <span class="text-danger text-center">{{ $message }}</span>
                    @enderror
            <div class="form-floating my-4">
              <input type="email" class="form-control" value="{{ old('email') }}" name="email" placeholder="Email address *" required="">
              <label for="contact_us_name">Email address *</label>
              <span class="text-danger"></span>
            </div>
             @error('email')
                        <span class="text-danger text-center">{{ $message }}</span>
                    @enderror
            <div class="my-4">
              <textarea class="form-control form-control_gray" name="message" placeholder="Your Message" cols="30"
                rows="8" required="">{{ old('message') }}</textarea>
              <span class="text-danger"></span>
            </div>
             @error('message')
                        <span class="text-danger text-center">{{ $message }}</span>
                    @enderror
            <div class="my-4">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </section>
  </main>
@endsection
