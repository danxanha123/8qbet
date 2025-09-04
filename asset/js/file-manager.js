/**
 * File Manager
 * Manages file uploads and deletions for the dashboard
 */

class FileManager {
    constructor() {
        this.uploadedFiles = this.loadUploadedFiles();
    }

    // Load list of uploaded files from localStorage
    loadUploadedFiles() {
        const saved = localStorage.getItem('uploaded_files');
        return saved ? JSON.parse(saved) : [];
    }

    // Save list of uploaded files to localStorage
    saveUploadedFiles() {
        localStorage.setItem('uploaded_files', JSON.stringify(this.uploadedFiles));
    }

    // Add a new uploaded file to the list
    addUploadedFile(fileName, fileData, fileType) {
        const fileInfo = {
            id: Date.now().toString(),
            fileName: fileName,
            fileData: fileData,
            fileType: fileType,
            uploadDate: new Date().toISOString(),
            size: fileData.length
        };
        
        this.uploadedFiles.push(fileInfo);
        this.saveUploadedFiles();
        return fileInfo.id;
    }

    // Remove a file from the list
    removeUploadedFile(fileId) {
        const index = this.uploadedFiles.findIndex(file => file.id === fileId);
        if (index !== -1) {
            const removedFile = this.uploadedFiles.splice(index, 1)[0];
            this.saveUploadedFiles();
            return removedFile;
        }
        return null;
    }

    // Get file by ID
    getFileById(fileId) {
        return this.uploadedFiles.find(file => file.id === fileId);
    }

    // Get all files of a specific type
    getFilesByType(fileType) {
        return this.uploadedFiles.filter(file => file.fileType === fileType);
    }

    // Generate a unique filename
    generateFileName(originalName, fileType) {
        const timestamp = Date.now();
        const extension = originalName.split('.').pop();
        return `${fileType}_${timestamp}.${extension}`;
    }

    // Convert file to base64
    fileToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    // Upload file and return file info
    async uploadFile(file, fileType) {
        try {
            const fileData = await this.fileToBase64(file);
            const fileName = this.generateFileName(file.name, fileType);
            const fileId = this.addUploadedFile(fileName, fileData, fileType);
            
            return {
                id: fileId,
                fileName: fileName,
                fileData: fileData,
                fileType: fileType
            };
        } catch (error) {
            console.error('Error uploading file:', error);
            throw error;
        }
    }

    // Delete file
    deleteFile(fileId) {
        const removedFile = this.removeUploadedFile(fileId);
        if (removedFile) {
            console.log('File deleted from storage:', removedFile.fileName);
            return removedFile;
        }
        return null;
    }

    // Get file statistics
    getFileStats() {
        const stats = {
            total: this.uploadedFiles.length,
            byType: {},
            totalSize: 0
        };

        this.uploadedFiles.forEach(file => {
            stats.byType[file.fileType] = (stats.byType[file.fileType] || 0) + 1;
            stats.totalSize += file.size;
        });

        return stats;
    }

    // Clean up old files (optional)
    cleanupOldFiles(daysOld = 30) {
        const cutoffDate = new Date();
        cutoffDate.setDate(cutoffDate.getDate() - daysOld);
        
        const initialCount = this.uploadedFiles.length;
        this.uploadedFiles = this.uploadedFiles.filter(file => {
            return new Date(file.uploadDate) > cutoffDate;
        });
        
        const removedCount = initialCount - this.uploadedFiles.length;
        if (removedCount > 0) {
            this.saveUploadedFiles();
            console.log(`Cleaned up ${removedCount} old files`);
        }
        
        return removedCount;
    }
}

// Global file manager instance
window.fileManager = new FileManager();
