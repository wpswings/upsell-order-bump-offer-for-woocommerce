(function() {
    const initWPSReports = () => {
        // Ensure WordPress React wrapper is available
        if (typeof wp === 'undefined' || !wp.element) {
            setTimeout(initWPSReports, 50);
            return;
        }

        const { useState, useEffect, useRef } = wp.element;

        // --- SHARED CONSTANTS ---
        const ICONS = {
            Sales: `<circle cx="25.5" cy="25.5" r="25.5" fill="#D9EEFF" /><path d="M25.4995 13.5996V38.5329" stroke="#2196F3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /><path d="M31.1666 18.1318H22.6666C21.6146 18.1318 20.6057 18.5498 19.8618 19.2936C19.1179 20.0375 18.7 21.0465 18.7 22.0985C18.7 23.1505 19.1179 24.1595 19.8618 24.9034C20.6057 25.6473 21.6146 26.0652 22.6666 26.0652H28.3333C29.3853 26.0652 30.3942 26.4831 31.1381 27.227C31.882 27.9709 32.3 28.9798 32.3 30.0318C32.3 31.0839 31.882 32.0928 31.1381 32.8367C30.3942 33.5806 29.3853 33.9985 28.3333 33.9985H18.7" stroke="#2196F3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />`,
            Views: `<circle cx="25.5" cy="25.5" r="25.5" fill="#D9EEFF" /><path d="M13 25.7576C13 25.7576 17.3788 17 25.0417 17C32.7045 17 37.0833 25.7576 37.0833 25.7576C37.0833 25.7576 32.7045 34.5152 25.0417 34.5152C17.3788 34.5152 13 25.7576 13 25.7576Z" stroke="#2196F3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /><path d="M25.2841 28.5682C27.0978 28.5682 28.5682 27.0978 28.5682 25.2841C28.5682 23.4703 27.0978 22 25.2841 22C23.4703 22 22 23.4703 22 25.2841C22 27.0978 23.4703 28.5682 25.2841 28.5682Z" stroke="#2196F3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />`,
            Success: `<circle cx="25" cy="25" r="25" fill="#D9EEFF" /><path d="M34.4444 23.8333V24.7278C34.4432 26.8243 33.7644 28.8643 32.5091 30.5434C31.2538 32.2226 29.4893 33.451 27.4788 34.0455C25.4683 34.6399 23.3196 34.5685 21.353 33.842C19.3864 33.1154 17.7073 31.7726 16.5662 30.0138C15.4251 28.255 14.8831 26.1745 15.0211 24.0825C15.159 21.9906 15.9695 19.9992 17.3317 18.4055C18.6938 16.8118 20.5347 15.7011 22.5796 15.239C24.6246 14.777 26.7641 14.9884 28.6792 15.8417" stroke="#2196F3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /><path d="M34.4443 16.9502L24.7221 26.6821L21.8054 23.7655" stroke="#2196F3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />`,
            CR: `<circle cx="25" cy="25" r="25" fill="#D9EEFF" /><path d="M25 36.667V24.167" stroke="#2196F3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /><path d="M32.5 36.667V16.667" stroke="#2196F3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /><path d="M17.5 36.667V31.667" stroke="#2196F3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /><path d="M15 24.167L27.5 11.667" stroke="#2196F3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /><path d="M21.25 11.667H27.5V17.917" stroke="#2196F3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />`
        };

        // --- SHARED PIE CHART COMPONENT ---
        const ChartComponent = ({ item, isFunnel }) => {
            const canvasRef = useRef(null);
            const chartRef = useRef(null);

            useEffect(() => {
                if (!canvasRef.current) return;
                const ctx = canvasRef.current.getContext('2d');
                if (chartRef.current) chartRef.current.destroy();

         const config = isFunnel ? {
    labels: [
        'Views',
        'Accepts',
        'Success',
        'Rejects',
        'Triggered Count',
        'Total Sales'
    ],
    colors: [
        '#90CAF9', // Views (soft blue)
        '#FFE082', // Accepts (soft amber)
        '#A5D6A7', // Success (soft green)
        '#EF9A9A', // Rejects (soft red)
        '#CE93D8', // Triggered (soft purple)
        '#FFCCBC'  // Sales (soft orange)
    ]
} : {
    labels: [
        'Views',
        'Accepts',
        'Success',
        'Rejects',
        'Total Sales'
    ],
    colors: [
        '#90CAF9', // Views
        '#FFE082', // Accepts
        '#A5D6A7', // Success
        '#B0BEC5', // Rejects (neutral grey)
        '#CE93D8'  // Sales
    ]
};


                chartRef.current = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: config.labels,
                        datasets: [{
                            data: item.data,
                            backgroundColor: config.colors,
                            borderWidth: 1
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { position: 'bottom' },
                            title: { display: true, text: item.name }
                        }
                    }
                });
                return () => { if (chartRef.current) chartRef.current.destroy(); };
            }, [item]);

            return wp.element.createElement('div', { className: 'wps_upsell_bumps_list-piechart-con' }, 
                wp.element.createElement('canvas', { ref: canvasRef })
            );
        };

        // --- MASTER APP COMPONENT ---
        const BaseReportsApp = ({ type, ajaxAction, title, storageKey }) => {
            const [data, setData] = useState({ summary: {}, items: [] });
            const [loading, setLoading] = useState(true);
            const [autoRefresh, setAutoRefresh] = useState(false);
            const [timer, setTimer] = useState(30);

            const fetchData = () => {
                setLoading(true);
                jQuery.ajax({
                    url: ajaxurl,
                    data: { action: ajaxAction },
                    success: (res) => {
                        if (res.success) setData(res.data);
                        setLoading(false);
                        setTimer(30);
                    }
                });
            };

            useEffect(() => { 
                fetchData(); 
                if (localStorage.getItem(storageKey) === 'true') setAutoRefresh(true);
            }, []);

            useEffect(() => {
                let interval;
                if (autoRefresh) {
                    interval = setInterval(() => {
                        setTimer((prev) => {
                            if (prev <= 1) { fetchData(); return 30; }
                            return prev - 1;
                        });
                    }, 1000);
                }
                return () => clearInterval(interval);
            }, [autoRefresh]);

            return wp.element.createElement('div', { className: 'wps_upsell_bumps_list' },
                // Header Controls
                wp.element.createElement('div', { className: 'wps-report-actions' },
                    autoRefresh && wp.element.createElement('span', { className: 'wps-timer-text' }, `Next update in ${timer}s`),
                    wp.element.createElement('label', { className: 'wps-auto-refresh-label' }, 
                        wp.element.createElement('input', { type: 'checkbox', checked: autoRefresh, onChange: (e) => {
                            setAutoRefresh(e.target.checked);
                            localStorage.setItem(storageKey, e.target.checked);
                        }}),
                        " Auto Refresh"
                    ),
                    wp.element.createElement('button', { className: 'button button-primary', onClick: fetchData, disabled: loading }, 
                        loading ? 'Updating...' : 'Refresh Now'
                    )
                ),

                // Statistics Cards
                wp.element.createElement('div', { className: 'wps_upsell_bumps_list-report' },
                    ['Sales', 'Views', 'Success', 'CR'].map(key => (
                        wp.element.createElement('div', { key, className: 'wps-bump-offer-cards' },
                            wp.element.createElement('div', { className: 'card-content' },
                                wp.element.createElement('h4', null, key === 'CR' ? 'Conversion Rate' : `Total ${key}`),
                                wp.element.createElement('p', {
                                    dangerouslySetInnerHTML: key === 'Sales' ? { __html: data.summary.sales } : undefined
                                }, key === 'Sales' ? null : (data.summary[key.toLowerCase()] || '0'))
                            ),
                            wp.element.createElement('div', { className: 'card-icon' },
                                wp.element.createElement('svg', { 
                                    width: "51", height: "51", viewBox: "0 0 51 51", fill: "none",
                                    dangerouslySetInnerHTML: { __html: ICONS[key] }
                                })
                            )
                        )
                    ))
                ),

                wp.element.createElement('h2', { className: 'wps_ubo_stats_heading_title' }, title),

                // Charts Grid
                wp.element.createElement('div', { className: 'wps_upsell_bumps_list-piechart' },
                    data.items.length > 0 ? 
                    data.items.map(item => wp.element.createElement(ChartComponent, { 
                        key: item.id, 
                        item, 
                        isFunnel: type === 'funnel' 
                    })) :
                    !loading && wp.element.createElement('p', { className: 'wps_upsell_bump_no_bump' }, `No ${title} Triggered!`)
                )
            );
        };

        // --- RENDER BOTH APPS IF ROOTS EXIST ---
        const bumpRoot = document.getElementById('wps-react-reports-root');
        if (bumpRoot) {
            wp.element.render(
                wp.element.createElement(BaseReportsApp, {
                    type: 'bump',
                    ajaxAction: 'wps_refresh_bump_stats',
                    title: 'Order Bump - Behavioral Analytics',
                    storageKey: 'wps_react_auto'
                }),
                bumpRoot
            );
        }

        const funnelRoot = document.getElementById('wps-upsell-funnel-root');
        if (funnelRoot) {
            wp.element.render(
                wp.element.createElement(BaseReportsApp, {
                    type: 'funnel',
                    ajaxAction: 'wps_refresh_upsell_funnel_stats',
                    title: 'Upsell Funnel - Behavioral Analytics',
                    storageKey: 'wps_upsell_auto'
                }),
                funnelRoot
            );
        }
    };

    initWPSReports();
})();