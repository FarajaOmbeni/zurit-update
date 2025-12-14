<template>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-top">
                <p class="footer-copyright">
                    © Zurit Consulting. All Rights Reserved.
                </p>
                <a href="#" class="footer-design-link">Design Information</a>
            </div>

            <div class="footer-disclaimer">
                <p>
                    <strong>No Investment Advice:</strong> All Content on this
                    site is for informational purposes only, you should not
                    construe any such information or other material as legal,
                    tax, investment, financial, or other advice. Nothing
                    contained on our Site constitutes a solicitation,
                    recommendation, endorsement, or offer by Zurit Consulting or
                    any third party service provider to buy or sell any
                    securities or other financial instruments in this or in any
                    other jurisdiction in which such solicitation or offer would
                    be unlawful under the securities laws of such jurisdiction.
                </p>
            </div>
        </div>
    </footer>
</template>

<script setup>
// No script needed for this simplified footer
</script>

<style scoped>
.footer {
    border-top: 1px solid rgba(255, 255, 255, 0.15);
    padding: 2rem 0 3rem;
    background: linear-gradient(135deg, #3b245d, #12041a);
    color: #ffffff;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

.footer-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-copyright {
    margin: 0;
    font-size: 0.9rem;
    color: #e3dcff;
}

.footer-design-link {
    font-size: 0.9rem;
    color: #e3dcff;
    text-decoration: underline;
    transition: color 0.3s ease;
}

.footer-design-link:hover {
    color: #ffffff;
}

.footer-disclaimer {
    margin: 0;
}

.footer-disclaimer p {
    margin: 0;
    font-size: 0.8rem;
    line-height: 1.7;
    color: #d0c9e6;
}

.footer-disclaimer strong {
    color: #ffffff;
}

@media (max-width: 768px) {
    .footer-top {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .footer-disclaimer p {
        font-size: 0.75rem;
    }
}
</style>
