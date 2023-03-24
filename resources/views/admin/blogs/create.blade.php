@extends('layouts.admin.admin')
@section('title', 'Add New Blog')
@push('style')
    <style>
        #description {
            height: 120px;
        }

        label.control-label[for="thumbnail"],
        label.control-label[for="galleries"] {
            display: block;
            width: 100%;
            height: 40px;
            line-height: 45px;
            text-align: center;
            background: #1172c2;
            color: #fff;
            font-size: 13px;
            text-transform: Uppercase;
            border-radius: 5px;
            cursor: pointer;
        }

        label.control-label[for="thumbnail"]+input,
        label.control-label[for="galleries"]+input {
            display: none;
        }

        #preview,
        #preview-g {
            display: flex;
            flex-wrap: wrap;
            gap: 2px;
            justify-content: flex-start;
            align-items: center;
        }

        #preview img,
        #preview-g img {
            width: 50px;
            height: 50px;
            display: block;
            object-fit: cover;
            background-size: cover;
        }

        #preview>div,
        #preview-g>div {
            margin: 0 2px;
        }
    </style>

    <style>
        .tag-container {
            background: #fff;
            padding: 3px;
            border: 1px solid #ddd;
            max-width: 290px;
            margin-bottom: 10px;
        }

        .tag-container .tag-input {
            height: 40px;
        }

        .tag-container .tag-input input {
            max-width: 100%;
            height: 100%;
            border: none;
            color: #666;
            font-size: 15px;
            outline: none;
            padding: 0 10px;
        }

        .tag-container .tags {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        .tag-container .tags .tag {
            display: inline-flex;
            font-size: 12px;
            margin-right: 4px;
            margin-bottom: 4px;
        }

        .tag-container .tags .tag-name {
            padding: 2px 5px;
            background: #ddd;
            word-break: break-word;
            color: #000;
        }

        .tag-container .tags .tag i {
            display: inline-flex;
            align-items: center;
            padding: 0 5px;
            background: indianred;
            color: #fff;
            cursor: pointer;
            user-select: none;
        }
    </style>
@endpush
@section('heading', 'Add Blog')

@section('body')
    @include('partials.admin.errors', ['errors' => $errors])
    <form role="form" action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-7">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Add a new blog
                    </div>
                    <div class="panel-body">

                        <div class="form-group @error('title') has-error @enderror">
                            <label class="control-label" for="title">Title</label>
                            <input type="text" name="title" class="form-control" id="title"
                                value="{{ old('title') }}">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="short_desc">Short Description</label>
                            <textarea name="short_desc" class="form-control" id="short_desc" placeholder="Optional">{{ old('short_desc') }}</textarea>
                        </div>
                        <div class="form-group @error('description') has-error @enderror">
                            <label class="control-label" for="description">Body</label>
                            <textarea name="description" class="form-control" id="description">{{ old('description') }}</textarea>
                        </div>
                        <div class="form-group @error('parent_id') has-error @enderror">
                            <label class="control-label" for="parent_id">Parent</label>
                            <select name="parent_id" id="parent_id" class="form-control">
                                <option value="" selected>Uncategorized</option>

                                @foreach ($categories as $category)
                                    @if (!$category->parent)
                                        <option value="{{ $category->id }}" @selected(old('parent_id') == $category->id)>
                                            {{ $category->name }}</option>
                                        @if ($category->children->count())
                                            @include('partials.admin.category-options', [
                                                'childrens' => $category->children,
                                                'prefix' => str_repeat('&nbsp;', $loop->depth),
                                            ])
                                        @endif
                                    @endif
                                @endforeach
                            </select>

                        </div>

                        <div class="form-group">
                            <label class="control-label">Created by</label>
                            <input type="text" value="Owner" readonly class="form-control">
                            <input type="number" name="user_id" value="{{ auth()->id() }}" hidden>
                        </div>

                    </div>

                </div>

            </div>
            <div class="col-md-5">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Set Blog Thumbnail
                    </div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label class="control-label" for="t_name">Image Name</label>
                            <input type="text" name="t_name" class="form-control" id="t_name"
                                value="{{ old('t_name') }}" placeholder="Optional">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="t_caption">Image Caption</label>
                            <input type="text" name="t_caption" class="form-control" id="t_caption"
                                value="{{ old('t_caption') }}" placeholder="Optional">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="t_details">About Image</label>
                            <textarea name="t_details" class="form-control" id="t_details" placeholder="Optional">{{ old('t_details') }}</textarea>
                        </div>
                        <div class="form-group @error('thumbnail') has-error @enderror">
                            <label class="control-label" for="thumbnail">Upload an Image</label>
                            <input type="file" name="thumbnail" class="form-control" id="thumbnail" accept="image/*">
                            <div id="preview"></div>
                        </div>

                    </div>

                </div>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Set Meta
                    </div>
                    <div class="panel-body">

                        <div class="tag-container">
                            <div class="tag-input">
                                <input type="text" id="tag-name" autocomplete="off" placeholder="Press enter to create">
                            </div>
                            <div class="tags"></div>
                            <input type="hidden" name="name" class="tag-name">
                        </div>

                        <hr>
                        <div class="form-group">
                            <input type="checkbox" name="can_commented" id="can_commented" style="width: auto" @checked(!is_null(old('can_commented')))>
                            <label for="can_commented">Apply comment for this post</label>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" name="featured" id="featured" style="width: auto" @checked(!is_null(old('featured')))>
                            <label for="featured">Make this post featured</label>
                        </div>

                    </div>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-7">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Set Blog Gallery
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="control-label" for="g_name">Gallery Name</label>
                            <input type="text" name="g_name" class="form-control" id="g_name"
                                value="{{ old('g_name') }}" placeholder="Optional">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="g_caption">Gallery Caption</label>
                            <input type="text" name="g_caption" class="form-control" id="g_caption"
                                value="{{ old('g_caption') }}" placeholder="Optional">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="g_details">About Gallery</label>
                            <textarea name="g_details" class="form-control" id="g_details" placeholder="Optional">{{ old('g_details') }}</textarea>
                        </div>
                        <div class="form-group @error('galleries') has-error @enderror">
                            <label class="control-label" for="galleries">Upload multiple Images</label>
                            <input type="file" name="galleries[]" class="form-control" id="galleries" multiple>
                            <div id="preview-g"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Add a Blog</button>
    </form>
@endsection
@push('script')
    <script>
        document.querySelector("#thumbnail").addEventListener("change", (e) => {
            const preview = document.querySelector("#preview")
            for (let i = 0; i < e.target.files.length; i++) {
                let image = document.createElement('img')
                let div = document.createElement('div')
                image.id = "image-" + i
                image.src = URL.createObjectURL(e.target.files[i])
                div.appendChild(image)
                preview.appendChild(div)
            }
        })
        document.querySelector("#galleries").addEventListener("change", (e) => {
            const preview = document.querySelector("#preview-g")
            for (let i = 0; i < e.target.files.length; i++) {
                let image = document.createElement('img')
                let div = document.createElement('div')
                image.id = "image-" + i
                image.src = URL.createObjectURL(e.target.files[i])
                div.appendChild(image)
                preview.appendChild(div)
            }
        })
    </script>

    <script>
        document.addEventListener('keydown', function(event) {
            if (event.code === 'Enter') {
                event.preventDefault()
            }
        });

        const tagInput = document.getElementById("tag-name")
        const tagsArea = document.querySelector(".tags")
        const tagHiddenInout = document.querySelector(".tag-name")

        var tags = []

        function createTag(value) {
            let tag = document.createElement("div")
            tag.setAttribute("class", "tag")

            let tagName = document.createElement("div")
            tagName.setAttribute("class", "tag-name")
            tagName.textContent = value

            let closeTag = document.createElement("i")
            closeTag.setAttribute("class", "close-tag")
            closeTag.setAttribute("data-item", value)
            closeTag.innerHTML = "&times;"

            tag.append(tagName)
            tag.append(closeTag)

            return tag
        }

        function reset() {
            tagsArea.querySelectorAll(".tag").forEach(function(tag) {
                tag.parentElement.removeChild(tag);
            })
        }

        function addTags() {
            reset()
            tags.slice().reverse().map(function(tag) {
                let input = createTag(tag)
                tagsArea.prepend(input)
            })
        }

        tagInput.addEventListener("keyup", function(e) {
            if (this.value != "") {
                tagInput.style.border = ""
                tagInput.style.borderRadius = ""
                if (e.key == "Enter") {
                    if (tags.includes(this.value) || this.value.match(/[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/g)) {
                        tagInput.style.border = "1px solid tomato"
                        tagInput.style.borderRadius = "3px"
                    } else {
                        tags.push(this.value.replace(/\s+/g, ''))
                        addTags()
                        tagHiddenInout.value = tags
                        e.target.value = ""
                    }
                }
            }
        })


        tagsArea.addEventListener("click", function(e) {
            if (e.target.tagName === "I") {
                const value = e.target.getAttribute("data-item");
                const index = tags.indexOf(value)
                tags = [...tags.slice(0, index), ...tags.slice(index + 1)]
                addTags()
                tagHiddenInout.value = tags
            }
        })

        @if (old('name'))
           @foreach (explode(",", old('name', [''])) as $name)
                tags.push("{{ $name }}")
           @endforeach
           addTags()
        @endif
    </script>
@endpush
