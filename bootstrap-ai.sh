# Định nghĩa các đường dẫn công cụ cố định
export KTX_TOOLS_DIR="$(pwd)/tools"
export GSD_PATH="$KTX_TOOLS_DIR/gsd"
export NEXUS_PATH="$KTX_TOOLS_DIR/gitnexus"
export STANDARDS_FILE="$(pwd)/STANDARDS.md"

# Ép AI Agent phải nhận diện lệnh
alias gsd="npx get-shit-done-cc@latest"
alias ktx-audit="node $KTX_TOOLS_DIR/impeccable/dist/index.js"
alias nexus="node $NEXUS_PATH/dist/index.js"