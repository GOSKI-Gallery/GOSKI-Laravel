<!DOCTYPE html>
<html lang="pt-br">

    @include('shared.head')

    <body class='min-h-screen bg-[#FAFAFA]'>
        <x-header />

        @auth
            <x-create-post-modal />
            <x-profile.edit-profile-modal :user="Auth::user()" />
            <x-notification-modal :notifications="[]" />
        @endauth

        <div class="itens-center mx-auto flex flex-col justify-between px-4 py-4">
            @yield('content')
        </div>

        <script>
            // Create Post Modal Script
            if (!window.createPostModalLoaded) {
                window.createPostModalLoaded = true;

                const setupCreatePostModal = () => {
                    const modal = document.getElementById('create-post-modal');
                    if (!modal) return;

                    const openBtn = document.getElementById('open-modal-btn');
                    const closeBtn = document.getElementById('close-modal-btn');
                    const closeX = document.getElementById('close-modal-x');
                    const input = document.getElementById('image_url');
                    const preview = document.getElementById('image-preview');
                    const placeholder = document.getElementById('upload-placeholder');
                    const description = document.getElementById('description');

                    if (openBtn) {
                        openBtn.addEventListener('click', (e) => {
                            e.preventDefault();
                            modal.classList.remove('hidden');
                            modal.classList.add('flex');
                            document.body.style.overflow = 'hidden';
                        });
                    }

                    if (input) {
                        input.addEventListener('change', () => {
                            const file = input.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    preview.src = e.target.result;
                                    preview.classList.remove('hidden');
                                    placeholder.classList.add('hidden');
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                    }

                    const closeModal = () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        document.body.style.overflow = 'auto';

                        setTimeout(() => {
                            if (preview) preview.src = '';
                            if (preview) preview.classList.add('hidden');
                            if (placeholder) placeholder.classList.remove('hidden');
                            if (input) input.value = '';
                            if (description) description.value = '';
                        }, 300);
                    };

                    if (closeBtn) closeBtn.addEventListener('click', closeModal);
                    if (closeX) closeX.addEventListener('click', closeModal);

                    modal.addEventListener('click', (e) => {
                        if (e.target === modal) closeModal();
                    });
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', setupCreatePostModal);
                } else {
                    setupCreatePostModal();
                }
            }

            // Edit Profile Modal Script
            if (!window.editProfileModalLoaded) {
                window.editProfileModalLoaded = true;

                const setupEditProfileModal = () => {
                    const modal = document.getElementById('edit-profile-modal');
                    if (!modal) return;
                    
                    const openBtn = document.getElementById('open-edit-profile-modal-btn');
                    const closeBtn = document.getElementById('close-edit-profile-modal-btn');
                    const closeX = document.getElementById('close-edit-profile-modal-x');
                    const avatarInput = document.getElementById('avatar');
                    const preview = document.getElementById('image-preview');
                    const placeholder = document.getElementById('upload-placeholder');
                    const usernameInput = document.getElementById('username');
                    const emailInput = document.getElementById('email');
                    const passwordInput = document.getElementById('password');

                    if (openBtn) {
                        openBtn.addEventListener('click', (e) => {
                            e.preventDefault();
                            modal.classList.remove('hidden');
                            modal.classList.add('flex');
                            document.body.style.overflow = 'hidden';
                        });
                    }

                    if (avatarInput) {
                        avatarInput.addEventListener('change', () => {
                            const file = avatarInput.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    if(preview) {
                                        preview.src = e.target.result;
                                        preview.classList.remove('hidden');
                                    }
                                    if(placeholder) {
                                        placeholder.classList.add('hidden');
                                    }
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                    }

                    const closeModal = () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        document.body.style.overflow = 'auto';
                    };

                    if (closeBtn) closeBtn.addEventListener('click', closeModal);
                    if (closeX) closeX.addEventListener('click', closeModal);

                    modal.addEventListener('click', (e) => {
                        if (e.target === modal) closeModal();
                    });
                };
                
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', setupEditProfileModal);
                } else {
                    setupEditProfileModal();
                }
            }

            // Notification Modal Script
            if (!window.notificationModalLoaded) {
                window.notificationModalLoaded = true;

                const setupNotificationModal = () => {
                    const notificationBtn = document.getElementById('notification-btn');
                    const notificationModal = document.getElementById('notification-modal');
                    if(!notificationModal) return;

                    const closeModalBtn = document.getElementById('close-modal');

                    if (notificationBtn) {
                        notificationBtn.addEventListener('click', () => {
                            fetch('{{ route("notifications.index") }}')
                                .then(response => response.json())
                                .then(data => {
                                    const notificationContent = document.getElementById('notification-content');
                                    notificationContent.innerHTML = '';
                                    data.forEach(notification => {
                                        const notificationElement = document.createElement('p');
                                        notificationElement.textContent = `Post: ${notification.description}`;
                                        notificationContent.appendChild(notificationElement);

                                        if(notification.likes) {
                                            notification.likes.forEach(like => {
                                                const likeElement = document.createElement('p');
                                                likeElement.textContent = `Liked by: ${like.user.name}`;
                                                notificationContent.appendChild(likeElement);
                                            });
                                        }

                                        if(notification.user.followers) {
                                            notification.user.followers.forEach(follower => {
                                                const followerElement = document.createElement('p');
                                                followerElement.textContent = `Followed by: ${follower.name}`;
                                                notificationContent.appendChild(followerElement);
                                            });
                                        }
                                    });
                                    notificationModal.classList.remove('hidden');
                                });
                        });
                    }

                    if(closeModalBtn) {
                        closeModalBtn.addEventListener('click', () => {
                            notificationModal.classList.add('hidden');
                        });
                    }
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', setupNotificationModal);
                } else {
                    setupNotificationModal();
                }
            }
        </script>
    </body>
</html>
