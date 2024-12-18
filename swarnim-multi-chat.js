document.addEventListener("DOMContentLoaded", function () {
    const container = document.querySelector("#swarnim-multi-chat-users");

    // Add User
    document.querySelector("#add-user").addEventListener("click", function () {
        const users = container.querySelectorAll(".user");
        const newIndex = users.length;
        const newUser = document.createElement("div");
        newUser.classList.add("user");
        newUser.dataset.index = newIndex;

        newUser.innerHTML = `
            <h4>User ${newIndex + 1}</h4>
            <label>Name:</label>
            <input type="text" name="swarnim_multi_chat_users[${newIndex}][name]" value="" />

            <label>Phone Number:</label>
            <input type="text" name="swarnim_multi_chat_users[${newIndex}][phone]" value="" />

            <label>Default Message:</label>
            <input type="text" name="swarnim_multi_chat_users[${newIndex}][message]" value="Hello! How can we help you?" />

            <label>Container Background Color:</label>
            <input type="color" name="swarnim_multi_chat_users[${newIndex}][container_bg_color]" value="#FFFFFF" />
            <label><input type="checkbox" name="swarnim_multi_chat_users[${newIndex}][container_bg_transparent]" value="1"> Transparent</label>

            <label>Text Color:</label>
            <input type="color" name="swarnim_multi_chat_users[${newIndex}][text_color]" value="#FFFFFF" />

            <label>Button Size (e.g., 60px):</label>
            <input type="text" name="swarnim_multi_chat_users[${newIndex}][size]" value="60px" />

            <label>Font Size (e.g., 14px):</label>
            <input type="text" name="swarnim_multi_chat_users[${newIndex}][font_size]" value="14px" />

            <label>Font Style:</label>
            <select name="swarnim_multi_chat_users[${newIndex}][font_style]">
                <option value="normal">Normal</option>
                <option value="italic">Italic</option>
            </select>

            <label>Font Weight:</label>
            <select name="swarnim_multi_chat_users[${newIndex}][font_weight]">
                <option value="normal">Normal</option>
                <option value="bold">Bold</option>
            </select>

            <button type="button" class="remove-user">Remove User</button>
        `;

        container.appendChild(newUser);
        attachRemoveHandlers();
    });

    // Remove User
    function attachRemoveHandlers() {
        container.querySelectorAll(".remove-user").forEach(function (button) {
            button.addEventListener("click", function () {
                this.parentNode.remove();
            });
        });
    }

    attachRemoveHandlers();
});
