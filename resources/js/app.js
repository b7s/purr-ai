import "./bootstrap";

document.addEventListener("alpine:init", () => {
    Alpine.data(
        "historyDropdown",
        (initialConversations, initialHasMorePages) => ({
            open: false,
            editModalOpen: false,
            editingId: null,
            editingTitle: "",
            loadingMore: false,
            conversations: initialConversations,
            hasMorePages: initialHasMorePages,

            getLivewireComponent() {
                // Find the Chat component specifically (it has the messages-container)
                const chatContainer =
                    document.getElementById("messages-container");
                if (chatContainer) {
                    const livewireEl = chatContainer.closest("[wire\\:id]");
                    if (livewireEl) {
                        const wireId = livewireEl.getAttribute("wire:id");
                        return Livewire.find(wireId);
                    }
                }
                return null;
            },

            loadConv(id) {
                const component = this.getLivewireComponent();
                if (component) {
                    component.call("loadConversation", id);
                }
            },

            startEdit(id, title) {
                this.editingId = id;
                this.editingTitle = title;
                this.editModalOpen = true;
                this.$nextTick(() => {
                    this.$refs.editInput?.select();
                });
            },

            saveEdit() {
                if (!this.editingTitle.trim()) {
                    return;
                }

                const component = this.getLivewireComponent();
                if (component) {
                    component
                        .call(
                            "saveTitleDirect",
                            this.editingId,
                            this.editingTitle
                        )
                        .then(() => {
                            const conv = this.conversations.find(
                                (c) => c.id === this.editingId
                            );
                            if (conv) {
                                conv.title = this.editingTitle;
                            }
                            this.editModalOpen = false;
                            this.editingId = null;
                            this.editingTitle = "";
                        });
                }
            },

            cancelEdit() {
                this.editModalOpen = false;
                this.editingId = null;
                this.editingTitle = "";
            },

            loadMore() {
                this.loadingMore = true;
                const component = this.getLivewireComponent();
                if (component) {
                    component
                        .call("loadMoreHistoryConversations")
                        .then((newConversations) => {
                            if (
                                newConversations &&
                                newConversations.length > 0
                            ) {
                                this.conversations = [
                                    ...this.conversations,
                                    ...newConversations,
                                ];
                                this.hasMorePages =
                                    newConversations.length >= 10;
                            } else {
                                this.hasMorePages = false;
                            }
                            this.loadingMore = false;
                        })
                        .catch(() => {
                            this.loadingMore = false;
                        });
                } else {
                    this.loadingMore = false;
                }
            },
        })
    );
});
