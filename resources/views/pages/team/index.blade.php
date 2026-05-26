{{-- resources/views/index.blade.php --}}

@php

$isCategoryAction = isset($_GET['action']);

$isEdit = $_GET['action'] ?? '';

$categoryName = $_GET['name'] ?? '';

$categories = [
    [
        "name" => "Project Manager",
        "count" => 2
    ],

    [
        "name" => "Programmer",
        "count" => 2
    ]
];

$members = [
    [
        "id" => 1,
        "avatar" => "KM",
        "name" => "Kian Meng",
        "role" => "Project Manager"
    ],

    [
        "id" => 2,
        "avatar" => "WL",
        "name" => "Wei Le",
        "role" => "Project Manager"
    ],

    [
        "id" => 3,
        "avatar" => "FL",
        "name" => "Fong Lam",
        "role" => "Programmer"
    ],

    [
        "id" => 4,
        "avatar" => "SS",
        "name" => "Swee Sheng",
        "role" => "Programmer"
    ]
];

@endphp

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        FlowSync | Team Management
    </title>

    {{-- BOOTSTRAP CSS --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    {{-- BOOTSTRAP ICONS --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- CUSTOM CSS --}}
    <link
        @vite(['resources/scss/app.scss'])

    {{-- FONT AWESOME --}}
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

<div class="app-layout">

    {{-- SIDEBAR --}}
    @include('components.fs-sidemenu')

    {{-- MAIN CONTENT --}}
    <main class="main-content">

        {{-- NAVBAR --}}
        @include('components.fs-header')

        {{-- DASHBOARD --}}
        <section class="dashboard">

            <div class="overview-card">

                {{-- HEADER --}}
                <div class="overview-header">

                    <div class="header-left">

                        <h2>

                            {{ $isCategoryAction
                                ? ($isEdit === 'edit-category'
                                    ? 'Edit Category'
                                    : 'Create Category')
                                : 'Department Overview' }}

                        </h2>

                    </div>

                </div>

                {{-- GRID --}}
                <div class="overview-grid">

                    {{-- LEFT PANEL --}}
                    @if($isCategoryAction)

                    <div class="panel category-form-panel">

                        <div class="panel-title">

                            <h3>

                                {{ $isEdit === 'edit-category'
                                    ? 'Edit Category'
                                    : 'Create Category' }}

                            </h3>

                        </div>

                        <label class="category-label">
                            Category Name
                        </label>

                        <input
                            class="category-input"
                            type="text"
                            id="categoryInput"
                            placeholder="Name"
                            value="{{ $categoryName }}">

                        <div class="button-group">

                            <button
                                id="cancelBtn"
                                class="cancel-btn">

                                Cancel

                            </button>

                            <button
                                id="confirmBtn"
                                class="confirm-btn">

                                {{ $isEdit === 'edit-category'
                                    ? 'Save'
                                    : 'Confirm' }}

                            </button>

                        </div>

                    </div>

                    @else

                    <div class="panel category-panel">

                        <div class="panel-title">

                            <h3>
                                Category List
                            </h3>

                            <button
                                id="addCategoryBtn"
                                type="button">

                                <i class="fa-solid fa-plus"></i>

                            </button>

                        </div>

                        {{-- CATEGORY LIST --}}
                        <ul id="categoryList">

                            @foreach($categories as $category)

                            <li data-category="{{ $category['name'] }}">

                                <div class="category-info">

                                    <span class="category-name">
                                        {{ $category['name'] }}
                                    </span>

                                </div>

                                <div class="category-actions">

                                    <span class="badge">
                                        {{ $category['count'] }}
                                    </span>

                                    <a
                                        href="{{ url('index?action=edit-category&name=' . urlencode($category['name'])) }}"
                                        class="edit-btn">

                                        <i class="bi bi-pencil-fill"></i>

                                    </a>

                                </div>

                            </li>

                            @endforeach

                        </ul>

                    </div>

                    @endif

                    {{-- MEMBER PANEL --}}
                    <div class="panel member-panel">

                        <div class="panel-title">

                            <h3>
                                Members
                            </h3>

                            <a
                                href="{{ url('create_member') }}"
                                id="addMemberBtn">

                                <i class="fa-solid fa-user-plus"></i>

                            </a>

                        </div>

                        {{-- MEMBER GRID --}}
                        <div class="member-grid"
                            id="memberGrid">

                            @foreach($members as $member)

                            <a
                                href="{{ url('view_member?id=' . $member['id']) }}"
                                class="member-card-link">

                                <div class="member-card"
                                    data-id="{{ $member['id'] }}"
                                    data-category="{{ $member['role'] }}">

                                    <div class="avatar">
                                        {{ $member['avatar'] }}
                                    </div>

                                    <div class="member-info">

                                        <h4>
                                            {{ $member['name'] }}
                                        </h4>

                                        <p>
                                            {{ $member['role'] }}
                                        </p>

                                    </div>

                                </div>

                            </a>

                            @endforeach

                        </div>

                    </div>

                </div>

            </div>

        </section>

    </main>

</div>

{{-- SCRIPT --}}
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

/* ADD CATEGORY */

const addCategoryBtn =
document.getElementById("addCategoryBtn");

if(addCategoryBtn){

    addCategoryBtn.addEventListener("click", () => {

        window.location.href =
        "{{ url('index?action=create-category') }}";

    });
}

/* CANCEL BUTTON */

const cancelBtn =
document.getElementById("cancelBtn");

if(cancelBtn){

    cancelBtn.addEventListener("click", () => {

        window.location.href =
        "{{ url('index') }}";

    });
}

/* CONFIRM BUTTON */

const confirmBtn =
document.getElementById("confirmBtn");

if(confirmBtn){

    confirmBtn.addEventListener("click", () => {

        const categoryName =
        document.getElementById("categoryInput")
        .value
        .trim();

        if(categoryName === ""){

            showToast(
                "Please enter category name",
                "error"
            );

            return;
        }

        showToast(

            "{{ $isEdit === 'edit-category'
                ? 'Category updated successfully'
                : 'Category created successfully' }}",

            "success"
        );

        setTimeout(() => {

            window.location.href =
            "{{ url('index') }}";

        }, 800);

    });
}

</script>

</body>
</html>