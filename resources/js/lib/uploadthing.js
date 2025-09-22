import { generateUploadButton, generateUploadDropzone } from "@uploadthing/vue";

export const UploadButton = generateUploadButton({
  url: "/api/uploadthing",
});

export const UploadDropzone = generateUploadDropzone({
  url: "/api/uploadthing",
});

