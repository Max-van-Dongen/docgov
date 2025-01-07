@extends('frontend', ["search" => false])
@section("content")

    <!-- Personal Profile Page -->
    <div class="container py-5">
        <h2 class="text-center mb-4">Personal Profile</h2>

        <!-- Profile Form -->
        <form method="POST" action="/personality">
            @csrf <!-- Laravel CSRF protection -->

            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" value="{{ session("name") ?? '' }}" required>
            </div>

            <!-- Age -->
            <div class="mb-3">
                <label for="age" class="form-label">Age</label>
                <input type="number" class="form-control" id="age" name="age" placeholder="Enter your age" value="{{ session("age") ?? '' }}" required>
            </div>

            <!-- Location -->
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location" placeholder="Enter your location" value="{{ session("location") ?? '' }}" required>
            </div>

            <!-- Interests -->
            <div class="mb-3">
                <label for="interests" class="form-label">Interests</label>
                <textarea class="form-control" id="interests" name="interests" placeholder="List your interests separated by commas">{{ session("interests") ?? '' }}</textarea>
            </div>

            <!-- Profession -->
            <div class="mb-3">
                <label for="profession" class="form-label">Profession</label>
                <input type="text" class="form-control" id="profession" name="profession" placeholder="Enter your profession" value="{{ session("profession") ?? '' }}">
            </div>

            <!-- Education -->
            <div class="mb-3">
                <label for="education" class="form-label">Education Level</label>
                <input type="text" class="form-control" id="education" name="education" placeholder="Enter your highest education level" value="{{ session("education") ?? '' }}">
            </div>

            <!-- Preferred Topics -->
            <div class="mb-3">
                <label for="preferred_topics" class="form-label">Preferred Topics</label>
                <textarea class="form-control" id="preferred_topics" name="preferred_topics" placeholder="List your preferred topics for personalized content">{{ session("preferred_topics") ?? '' }}</textarea>
            </div>

            <!-- Save Button -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Save Profile</button>
            </div>
        </form>
    </div>

@endsection
