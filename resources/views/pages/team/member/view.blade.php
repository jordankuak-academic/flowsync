{{-- resources/views/pages/team/member/view.blade.php --}}

@php

$memberId = request('id', 1);

$members = [

    1 => [
        "id" => "EPY0001",
        "username" => "staff001",
        "password" => "Employee1234",
        "name" => "Kian Meng",
        "ic" => "010914014772",
        "email" => "kianmeng@gmail.com",
        "phone" => "60123456789",
        "department" => "Developer",
        "category" => "Project Manager"
    ],

    2 => [
        "id" => "EPY0002",
        "username" => "staff002",
        "password" => "Employee2341",
        "name" => "Wei Le",
        "ic" => "011114019316",
        "email" => "weile@gmail.com",
        "phone" => "60111222333",
        "department" => "Developer",
        "category" => "Project Manager"
    ],

    3 => [
        "id" => "EPY0003",
        "username" => "staff003",
        "password" => "Employee3412",
        "name" => "Fong Lam",
        "ic" => "020214017065",
        "email" => "fonglam@gmail.com",
        "phone" => "60199887766",
        "department" => "Developer",
        "category" => "Programmer"
    ],

    4 => [
        "id" => "EPY0004",
        "username" => "staff004",
        "password" => "Employee4123",
        "name" => "Swee Sheng",
        "ic" => "030314016324",
        "email" => "sweesheng@gmail.com",
        "phone" => "60166778899",
        "department" => "Developer",
        "category" => "Programmer"
    ]

];

$member = $members[$memberId];

@endphp

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        View Member
    </title>

    @vite(['resources/scss/app.scss'])

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

<div class="app-layout">

    {{-- SIDEBAR --}}
    @include('components.fs-sidemenu')

    {{-- MAIN CONTENT --}}
    <div class="main-content">

        {{-- NAVBAR --}}
        @include('components.fs-header')

        <section class="dashboard">

            <div class="overview-card">

                {{-- PAGE HEADER --}}
                <div class="overview-header">

                    <div class="header-left">

                        <h2>
                            View Member
                        </h2>

                    </div>

                </div>

                {{-- MEMBER LAYOUT --}}
                <div class="member-layout">

                    {{-- LOGIN PANEL --}}
                    <div class="panel login-panel">

                        <div class="panel-title">

                            <h3>
                                Login Info
                            </h3>

                        </div>

                        <div class="form-group">

                            <label>
                                Username
                            </label>

                            <input
                                type="text"
                                class="form-input"
                                value="{{ $member['username'] }}"
                                readonly>

                        </div>

                        <div class="form-group">

                            <label>
                                Password
                            </label>

                            <input
                                type="password"
                                class="form-input"
                                value="{{ $member['password'] }}"
                                readonly>

                        </div>

                    </div>

                    {{-- PERSONAL PANEL --}}
                    <div class="panel personal-panel">

                        {{-- TOP --}}
                        <div class="panel-top">

                            <h3>
                                Personal Information
                            </h3>

                            <div class="member-id">

                                ID :
                                {{ $member['id'] }}

                            </div>

                        </div>

                        {{-- FORM GRID --}}
                        <div class="member-form-grid">

                            {{-- NAME --}}
                            <div class="form-group">

                                <label>
                                    Name
                                </label>

                                <input
                                    type="text"
                                    class="form-input"
                                    value="{{ $member['name'] }}"
                                    readonly>

                            </div>

                            {{-- IC --}}
                            <div class="form-group">

                                <label>
                                    IC No
                                </label>

                                <input
                                    type="text"
                                    class="form-input"
                                    value="{{ $member['ic'] }}"
                                    readonly>

                            </div>

                            {{-- EMAIL --}}
                            <div class="form-group">

                                <label>
                                    Email
                                </label>

                                <input
                                    type="email"
                                    class="form-input"
                                    value="{{ $member['email'] }}"
                                    readonly>

                            </div>

                            {{-- PHONE --}}
                            <div class="form-group">

                                <label>
                                    Telephone No
                                </label>

                                <input
                                    type="text"
                                    class="form-input"
                                    value="{{ $member['phone'] }}"
                                    readonly>

                            </div>

                            {{-- DEPARTMENT --}}
                            <div class="form-group">

                                <label>
                                    Department
                                </label>

                                <input
                                    type="text"
                                    class="form-input"
                                    value="{{ $member['department'] }}"
                                    readonly>

                            </div>

                            {{-- CATEGORY --}}
                            <div class="form-group">

                                <label>
                                    Category
                                </label>

                                <input
                                    type="text"
                                    class="form-input"
                                    value="{{ $member['category'] }}"
                                    readonly>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- ACTION BAR --}}
                <div class="member-action-bar">

                    {{-- LEFT --}}
                    <button
                        class="cancel-btn"
                        type="button"
                        onclick="window.location.href='{{ url('/team') }}'">

                        Cancel

                    </button>

                    {{-- RIGHT --}}
                    <div class="action-right">

                        <button
                            class="delete-btn"
                            type="button"
                            id="deleteMemberBtn">

                            Delete

                        </button>

                        <button
                            class="confirm-btn"
                            type="button"
                            onclick="window.location.href='{{ url('/edit_member?id=' . $memberId) }}'">

                            Edit

                        </button>

                    </div>

                </div>

            </div>

        </section>

    </div>

</div>

{{-- DELETE MODAL --}}
<div
    class="delete-modal"
    id="deleteModal">

    <div class="delete-modal-content">

        <div class="delete-icon">

            <i class="bi bi-trash-fill"></i>

        </div>

        <h3>
            Delete Member?
        </h3>

        <p>
            This action cannot be undone.
        </p>

        <div class="delete-modal-actions">

            <button
                class="cancel-delete-btn"
                id="cancelDeleteBtn">

                Cancel

            </button>

            <button
                class="confirm-delete-btn"
                id="confirmDeleteBtn">

                Delete

            </button>

        </div>

    </div>

</div>

<script>

const deleteBtn =
document.getElementById("deleteMemberBtn");

const deleteModal =
document.getElementById("deleteModal");

const cancelDeleteBtn =
document.getElementById("cancelDeleteBtn");

const confirmDeleteBtn =
document.getElementById("confirmDeleteBtn");


/* OPEN MODAL */

deleteBtn.addEventListener("click", () => {

    deleteModal.classList.add("show");

});


/* CLOSE MODAL */

cancelDeleteBtn.addEventListener("click", () => {

    deleteModal.classList.remove("show");

});


/* CONFIRM DELETE */

confirmDeleteBtn.addEventListener("click", () => {

    window.location.href =
    "{{ url('/team') }}";

});

</script>

</body>
</html>