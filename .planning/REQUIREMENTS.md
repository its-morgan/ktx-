# Requirements

## Functional Requirements

### 1. Room Management
- **must-have:** CRUD operations for Rooms and Room Types.
- **must-have:** Track room occupancy and status (Available, Full, Maintenance).
- **should-have:** Manage assets (Vattu) within each room.

### 2. Student Management
- **must-have:** Student registration and profile management.
- **must-have:** Room assignment and transfer logic.
- **should-have:** Discipline (Kyluat) tracking for students.

### 3. Financials & Contracts
- **must-have:** Contract (Hopdong) generation and signing workflow.
- **must-have:** Monthly invoicing (Hoadon) for rent and services.
- **should-have:** Debt (Congno) tracking and payment history.

### 4. Operations & Maintenance
- **must-have:** Maintenance reporting (Baohong) for rooms.
- **should-have:** Maintenance history (Lichsubaotri) tracking.
- **must-have:** System-wide and individual notifications (Thongbao).

### 5. Portals
- **Admin Portal:** Full management suite for all entities.
- **Student Portal:** Access to own room info, assets, invoices, and maintenance reporting.

## Non-Functional Requirements
- **Performance:** Optimized queries for large student lists.
- **UX/UI:** Consistent, modern interface using Tailwind and Flowbite.
- **Localization:** Professional Vietnamese language throughout.
- **Security:** Strict RBAC (Role-Based Access Control).
