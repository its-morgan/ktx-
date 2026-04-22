# AI Workflow Index — KTX Management System

Đây là bản đồ nhanh của toàn bộ hệ thống AI Agentic Workflow trong dự án.

---

## 🔒 Luật hành vi (Đọc trước tiên)

| File | Nội dung |
|------|---------|
| [`AGENTS.md`](../AGENTS.md) | Quy tắc bắt buộc cho AI: Impact Analysis, Never Do, Tool reference |

---

## 🧠 Bộ nhớ dự án (`.planning/`)

| File | Nội dung |
|------|---------|
| [`PROJECT.md`](../PROJECT.md) | Tầm nhìn, phạm vi dự án |
| [`REQUIREMENTS.md`](../REQUIREMENTS.md) | Danh sách yêu cầu chức năng |
| [`ROADMAP.md`](../ROADMAP.md) | Lộ trình, phases, tiến độ hiện tại |
| [`STATE.md`](../STATE.md) | Các quyết định đã chốt, không hỏi lại |
| [`CONTEXT.md`](../CONTEXT.md) | **Design Contract** — font, màu, glassmorphism |
| [`config.json`](../config.json) | Cấu hình GSD skills injection |
| [`codebase/CODEBASE.md`](CODEBASE.md) | Stack, Architecture, Directory structure |
| [`codebase/STANDARDS.md`](STANDARDS.md) | Conventions, Known Issues, Patterns |

---

## 🛠️ Skills GSD (`.agent/skills/`)

### Workflow (Quy trình làm việc)
| Skill | Khi nào dùng |
|-------|-------------|
| `gsd-discuss-phase` | Thảo luận yêu cầu trước khi làm |
| `gsd-spec-phase` | Làm rõ requirement, tránh ambiguity |
| `gsd-plan-phase` | Lập kế hoạch chi tiết trước khi code |
| `gsd-execute-phase` | Thực thi an toàn theo plan |
| `gsd-fast` | Tác vụ nhỏ, không cần plan đầy đủ |
| `gsd-next` | Tự động chuyển bước tiếp theo |
| `gsd-progress` | Kiểm tra tiến độ roadmap |

### Chất lượng (Quality)
| Skill | Khi nào dùng |
|-------|-------------|
| `gsd-code-review` | Review code sau mỗi thay đổi |
| `gsd-code-review-fix` | Tự động fix lỗi từ review |
| `gsd-verify-work` | UAT, kiểm tra tính năng sau build |
| `gsd-debug` | Debug có hệ thống, lưu trạng thái |
| `gsd-secure-phase` | Kiểm tra bảo mật sau hoàn thành |

### UI/UX
| Skill | Khi nào dùng |
|-------|-------------|
| `gsd-ui-phase` | Thiết kế UI contract trước khi code |
| `gsd-ui-review` | Review UI/UX sau implement |
| `ui-ux-pro-max/` | Bộ não thiết kế — tokens, styles, typography |

### Tài liệu
| Skill | Khi nào dùng |
|-------|-------------|
| `gsd-docs-update` | Cập nhật tài liệu sau thay đổi code |

---

## 🤖 Agents GSD (`.agent/agents/`)

| Agent | Vai trò |
|-------|---------|
| `gsd-planner.md` | Tư duy lập kế hoạch chi tiết |
| `gsd-executor.md` | Thực thi code có kỷ luật |
| `gsd-verifier.md` | Kiểm tra chất lượng đầu ra |
| `gsd-debugger.md` | Debug phức tạp, multi-step |
| `gsd-code-reviewer.md` | Review bugs, security, code quality |
| `gsd-ui-auditor.md` | Kiểm tra giao diện theo design system |

---

## 🔍 Code Intelligence (GitNexus)

| Tool | Khi nào dùng |
|------|-------------|
| `gitnexus_impact` | **Bắt buộc** trước khi sửa bất kỳ hàm nào |
| `gitnexus_query` | Tìm code theo khái niệm |
| `gitnexus_context` | 360° view về một symbol |
| `gitnexus_detect_changes` | Kiểm tra trước khi commit |
| `gitnexus_rename` | Đổi tên symbol an toàn |

---

## 🎨 Laravel Skills (`.claude/skills/`)

| Skill | Nội dung |
|-------|---------|
| `generated/auth/` | Pattern authentication Laravel |
| `generated/controllers/` | Pattern viết Controllers chuẩn dự án |
| `generated/feature/` | Pattern thêm feature mới |
| `gitnexus/` | Hướng dẫn chi tiết từng GitNexus tool |
