class OfflineAttendance {
    constructor() {
        this.dbName = "afsc-attendance-db";
        this.dbVersion = 1;
        this.storeName = "attendance-records";
        this.db = null;
        this.initDB();
    }

    async initDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.dbVersion);

            request.onerror = (event) => {
                console.error("IndexedDB error:", event.target.error);
                reject(event.target.error);
            };

            request.onsuccess = (event) => {
                this.db = event.target.result;
                console.log("IndexedDB initialized");
                resolve(this.db);
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                if (!db.objectStoreNames.contains(this.storeName)) {
                    db.createObjectStore(this.storeName, { keyPath: "id" });
                }
            };
        });
    }

    async saveAttendance(groupId, date, studentId, status) {
        if (!this.db) await this.initDB();

        const record = {
            id: `${groupId}-${date}-${studentId}`,
            groupId,
            date,
            studentId,
            status,
            timestamp: new Date().toISOString(),
            synced: false,
        };

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(
                [this.storeName],
                "readwrite",
            );
            const store = transaction.objectStore(this.storeName);
            const request = store.put(record);

            request.onsuccess = () => resolve(record);
            request.onerror = (e) => reject(e.target.error);
        });
    }

    async getUnsyncedRecords() {
        if (!this.db) await this.initDB();

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(
                [this.storeName],
                "readonly",
            );
            const store = transaction.objectStore(this.storeName);
            const request = store.getAll();

            request.onsuccess = (event) => {
                const records = event.target.result;
                const unsynced = records.filter((r) => !r.synced);
                resolve(unsynced);
            };
            request.onerror = (e) => reject(e.target.error);
        });
    }

    async markAsSynced(id) {
        if (!this.db) await this.initDB();

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(
                [this.storeName],
                "readwrite",
            );
            const store = transaction.objectStore(this.storeName);
            // We can delete synced records to keep DB small, or mark them.
            // Let's delete them for now to save space.
            const request = store.delete(id);

            request.onsuccess = () => resolve();
            request.onerror = (e) => reject(e.target.error);
        });
    }

    async getGroupAttendance(groupId, date) {
        if (!this.db) await this.initDB();

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(
                [this.storeName],
                "readonly",
            );
            const store = transaction.objectStore(this.storeName);
            const request = store.getAll();

            request.onsuccess = (event) => {
                const records = event.target.result;
                // Filter in memory for now as IDB queries are complex without indices
                const groupRecords = records.filter(
                    (r) => r.groupId === groupId && r.date === date,
                );
                resolve(groupRecords);
            };
            request.onerror = (e) => reject(e.target.error);
        });
    }
}

window.OfflineAttendance = new OfflineAttendance();
