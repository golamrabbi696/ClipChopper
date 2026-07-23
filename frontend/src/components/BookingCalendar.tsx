import React, { useEffect, useMemo, useState } from 'react';
import { Calendar } from '@astryxdesign/core/Calendar';
import { defineTheme, Theme } from '@astryxdesign/core/theme';

import '@astryxdesign/core/astryx.css';

type Props = {
  initialDate?: string;
  initialTime?: string;
};

function todayIsoDate(): string {
  const now = new Date();
  const yyyy = now.getFullYear();
  const mm = String(now.getMonth() + 1).padStart(2, '0');
  const dd = String(now.getDate()).padStart(2, '0');
  return `${yyyy}-${mm}-${dd}`;
}

const clipChopperTheme = defineTheme({
  name: 'clipchopper',
  tokens: {
    '--color-accent': 'var(--gold)',
    '--color-accent-muted': 'var(--gold-lt)',
    '--color-on-accent': 'var(--navy)',
    '--color-background-body': 'var(--bg)',
    '--color-background-surface': 'var(--card)',
    '--color-background-muted': 'var(--bg-2)',
    '--color-text-primary': 'var(--ink)',
    '--color-text-secondary': 'var(--ink-2)',
    '--color-text-disabled': 'var(--faint)',
    '--color-text-accent': 'var(--gold-dk)',
    '--color-border': 'var(--border-2)',
    '--color-border-emphasized': 'var(--border-2)',
    '--radius-container': 'var(--r)',
    '--radius-element': 'var(--r-sm)',
    '--radius-inner': 'var(--r-sm)',
    '--spacing-1': '4px',
    '--spacing-2': '8px',
    '--spacing-3': '12px',
    '--spacing-4': '16px',
    '--spacing-5': '20px',
    '--spacing-6': '24px',
  },
});

function buildTimesEvery30Minutes(start = '09:00', end = '18:00'): string[] {
  const [startH, startM] = start.split(':').map(Number);
  const [endH, endM] = end.split(':').map(Number);

  let minutes = startH * 60 + startM;
  const endMinutes = endH * 60 + endM;
  const times: string[] = [];

  while (minutes <= endMinutes) {
    const hh = String(Math.floor(minutes / 60)).padStart(2, '0');
    const mm = String(minutes % 60).padStart(2, '0');
    times.push(`${hh}:${mm}`);
    minutes += 30;
  }

  return times;
}

export default function BookingCalendar({ initialDate, initialTime }: Props) {
  const [callDate, setCallDate] = useState<string | undefined>(initialDate);
  const [callTime, setCallTime] = useState<string>(initialTime ?? '');

  const minDate = useMemo(() => todayIsoDate(), []);
  const tz = useMemo(() => Intl.DateTimeFormat().resolvedOptions().timeZone, []);
  const times = useMemo(() => buildTimesEvery30Minutes('09:00', '18:00'), []);

  useEffect(() => {
    const onReset = () => {
      setCallDate(initialDate);
      setCallTime(initialTime ?? '10:00');
    };
    window.addEventListener('cc:booking:reset', onReset);
    return () => window.removeEventListener('cc:booking:reset', onReset);
  }, [initialDate, initialTime]);

  return (
    <Theme theme={clipChopperTheme}>
      <div className="cc-booking-calendar">
        <div className="cc-booking-calendar__grid">
          <Calendar
            mode="single"
            value={callDate}
            onChange={(next) => setCallDate(next)}
            min={minDate}
          />
        </div>

        <div className="cc-booking-calendar__time">
          <label htmlFor="cf-time">Preferred time *</label>
          <select
            id="cf-time"
            name="call_time"
            required
            value={callTime}
            onChange={(e) => setCallTime(e.target.value)}
          >
            <option value="" disabled>
              — Select a time —
            </option>
            {times.map((t) => (
              <option key={t} value={t}>
                {t}
              </option>
            ))}
          </select>
          <small className="cc-booking-calendar__hint">
            Your timezone: {tz}
          </small>
        </div>

        <div className="cc-booking-calendar__selected" aria-live="polite">
          {callDate ? `Selected date: ${callDate}` : 'Select a date above.'}
        </div>

        <input type="hidden" name="call_date" value={callDate ?? ''} />
        <input type="hidden" name="timezone" value={tz} />
      </div>
    </Theme>
  );
}
