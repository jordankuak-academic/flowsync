{{-- resources/views/pages/team/member/create.blade.php --}}

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Create Member
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
                <div class="create-header">

                    <h2>
                        Create Member
                    </h2>

                </div>


                <div class="member-layout">

                    {{-- LOGIN PANEL --}}
                    <div class="login-panel">

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
                                placeholder="Username">

                        </div>

                        <div class="form-group">

                            <label>
                                Password
                            </label>

                            <input
                                type="password"
                                class="form-input"
                                placeholder="Password">

                        </div>

                    </div>



                    {{-- PERSONAL PANEL --}}
                    <div class="personal-panel">

                        <div class="panel-top">

                            <h3>
                                Personal Information
                            </h3>

                        </div>

                        <div class="member-form-grid">

                            {{-- NAME --}}
                            <div class="form-group">

                                <label>
                                    Name
                                </label>

                                <input
                                    type="text"
                                    class="form-input"
                                    placeholder="Name">

                            </div>

                            {{-- IC --}}
                            <div class="form-group">

                                <label>
                                    IC No
                                </label>

                                <input
                                    type="text"
                                    class="form-input"
                                    placeholder="Identity Card Number">

                            </div>

                            {{-- EMAIL --}}
                            <div class="form-group">

                                <label>
                                    Email
                                </label>

                                <input
                                    type="email"
                                    class="form-input"
                                    placeholder="xxx@xxxmail.com">

                            </div>

                            {{-- PHONE --}}
                            <div class="form-group">

                                <label>
                                    Telephone No
                                </label>

                                <input
                                    type="text"
                                    class="form-input"
                                    placeholder="601xxxxxxxx">

                            </div>

                            {{-- DEPARTMENT --}}
                            <div class="form-group">

                                <label>
                                    Department
                                </label>

                                <input
                                    type="text"
                                    class="form-input"
                                    value="Developer"
                                    readonly>

                            </div>

                            {{-- CATEGORY --}}
                            <div class="form-group">

                                <label>
                                    Category
                                </label>

                                <select class="form-input">

                                    <option
                                        value=""
                                        selected
                                        disabled>

                                        Select Category

                                    </option>

                                    <option>
                                        Project Manager
                                    </option>

                                    <option>
                                        Programmer
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ACTION BAR --}}
                <div class="member-action-bar">

                    <button
                        class="cancel-btn"
                        type="button"
                        onclick="window.location.href='{{ url('/team') }}'">

                        Cancel

                    </button>

                    <div class="action-right">

                        <button
                            class="confirm-btn"
                            type="button">

                            Confirm

                        </button>

                    </div>

                </div>

            </div>

        </section>

    </div>

</div>

<script>

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


document.querySelector(".confirm-btn")
.addEventListener("click", () => {

    const inputs =
    document.querySelectorAll(".form-input");

    let hasEmpty = false;

    inputs.forEach(input => {

        if(
            input.value.trim() === ""
            &&
            !input.hasAttribute("readonly")
        ){

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
        "Member created successfully",
        "success"
    );

    setTimeout(() => {

        window.location.href =
        "{{ url('/team') }}";

    }, 700);

});

</script>

</body>
</html>