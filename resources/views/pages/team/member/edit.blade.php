{{-- resources/views/pages/team/member/edit.blade.php --}}

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
        Edit Member
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

                {{-- TITLE --}}
                <div class="create-header">

                    <h2>
                        Edit Member
                    </h2>

                </div>



                {{-- MEMBER LAYOUT --}}
                <div class="member-layout">


                    {{-- LOGIN PANEL --}}
                    <div class="login-panel">

                        <div class="panel-title">

                            <h3>
                                Login Info
                            </h3>

                        </div>


                        {{-- USERNAME --}}
                        <div class="form-group">

                            <label>
                                Username
                            </label>

                            <input
                                type="text"
                                class="form-input"
                                id="username"
                                value="{{ $member['username'] }}">

                        </div>


                        {{-- PASSWORD --}}
                        <div class="form-group">

                            <label>
                                Password
                            </label>

                            <div class="password-wrapper">

                                <input
                                    type="password"
                                    class="form-input"
                                    id="password"
                                    value="{{ $member['password'] }}">

                                <button
                                    type="button"
                                    class="toggle-password"
                                    id="togglePassword">

                                    <i class="bi bi-eye-fill"></i>

                                </button>

                            </div>

                        </div>

                    </div>



                    {{-- PERSONAL PANEL --}}
                    <div class="personal-panel">


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
                                    id="name"
                                    value="{{ $member['name'] }}">

                            </div>



                            {{-- IC --}}
                            <div class="form-group">

                                <label>
                                    IC No
                                </label>

                                <input
                                    type="text"
                                    class="form-input"
                                    id="ic"
                                    value="{{ $member['ic'] }}">

                            </div>



                            {{-- EMAIL --}}
                            <div class="form-group">

                                <label>
                                    Email
                                </label>

                                <input
                                    type="email"
                                    class="form-input"
                                    id="email"
                                    value="{{ $member['email'] }}">

                            </div>



                            {{-- PHONE --}}
                            <div class="form-group">

                                <label>
                                    Telephone No
                                </label>

                                <input
                                    type="text"
                                    class="form-input"
                                    id="phone"
                                    value="{{ $member['phone'] }}">

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

                                <select
                                    class="form-input"
                                    id="category">

                                    <option
                                        {{ $member['category'] === 'Project Manager' ? 'selected' : '' }}>

                                        Project Manager

                                    </option>

                                    <option
                                        {{ $member['category'] === 'Programmer' ? 'selected' : '' }}>

                                        Programmer

                                    </option>

                                </select>

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
                        onclick="window.location.href='{{ url('/view_member?id=' . $memberId) }}'">

                        Cancel

                    </button>



                    {{-- RIGHT --}}
                    <div class="action-right">

                        <button
                            class="confirm-btn"
                            type="button"
                            id="saveBtn">

                            Save

                        </button>

                    </div>

                </div>

            </div>

        </section>

    </div>

</div>



<script>

/* =========================
   TOAST
========================= */

function showToast(message, type){

    const toast = document.createElement("div");

    toast.className =
    "custom-toast " + type;

    toast.innerHTML = message;

    document.body.appendChild(toast);

    setTimeout(() => {

        toast.classList.add("show");

    }, 100);

    setTimeout(() => {

        toast.classList.remove("show");

        setTimeout(() => {

            toast.remove();

        }, 300);

    }, 2200);
}



/* =========================
   SAVE BUTTON
========================= */

document.getElementById("saveBtn")
.addEventListener("click", () => {

    const requiredFields = [

        document.getElementById("username"),
        document.getElementById("password"),
        document.getElementById("name"),
        document.getElementById("ic"),
        document.getElementById("email"),
        document.getElementById("phone")

    ];


    let hasEmpty = false;


    requiredFields.forEach(field => {

        if(field.value.trim() === ""){

            hasEmpty = true;
        }

    });


    if(hasEmpty){

        showToast(
            "Please fill in all information",
            "error"
        );

        return;
    }


    showToast(
        "Update Successfully",
        "success"
    );


    setTimeout(() => {

        window.location.href =
        "{{ url('/view_member?id=' . $memberId) }}";

    }, 800);

});


/* =========================
   PASSWORD TOGGLE
========================= */

const togglePassword =
document.getElementById("togglePassword");

const passwordInput =
document.getElementById("password");


togglePassword.addEventListener("click", () => {

    const type =
    passwordInput.getAttribute("type");

    if(type === "password"){

        passwordInput.setAttribute(
            "type",
            "text"
        );

        togglePassword.innerHTML =
        '<i class="bi bi-eye-slash-fill"></i>';

    }

    else{

        passwordInput.setAttribute(
            "type",
            "password"
        );

        togglePassword.innerHTML =
        '<i class="bi bi-eye-fill"></i>';
    }

});

</script>

</body>
</html>