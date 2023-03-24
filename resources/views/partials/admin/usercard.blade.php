<style>
    .users-card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        max-width: 300px;
        margin: auto;
        text-align: center;
        font-family: arial;
    }

    .users-card .title {
        color: grey;
        font-size: 18px;
    }

    .users-card .btn {
        border: none;
        outline: 0;
        display: inline-block;
        padding: 8px;
        color: white;
        background-color: #000;
        text-align: center;
        cursor: pointer;
        width: 100%;
        font-size: 18px;
    }

    .users-card a {
        text-decoration: none;
        color: black;
    }

    .users-card .btn:hover {
        opacity: 0.7;
    }
</style>

<div class="users-card">
    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSgXKfFxgpqBoP6xhM38YNZihAVYGlbGzjUcQ&usqp=CAU"
        alt="John" style="width:50%">
    <h1>{{ $user->name }}</h1>
    <p class="title">{{ $user->isAdmin() ? 'Administrator' : 'Regular User' }}</p>
    <p>{{ $user->email }}</p>
    <div style="margin: 24px 0;">
        @include('partials.admin.actions', ['user' => $user])
    </div>
    <p><a href="{{ $url }}" class="btn">Back</a></p>

</div>
