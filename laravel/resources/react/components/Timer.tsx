import { useEffect, useState, useRef, useCallback } from 'react';
import { Clock } from 'lucide-react';

interface TimerProps {
  initialSeconds: number;
  onExpire?: () => void;
}

export function Timer({ initialSeconds, onExpire }: TimerProps) {
  const [secondsRemaining, setSecondsRemaining] = useState(Math.floor(initialSeconds));
  const intervalRef = useRef<NodeJS.Timeout | null>(null);
  const onExpireRef = useRef(onExpire);
  
  // Keep onExpire ref updated
  useEffect(() => {
    onExpireRef.current = onExpire;
  }, [onExpire]);

  // Start the timer
  useEffect(() => {
    // Clear any existing interval
    if (intervalRef.current) {
      clearInterval(intervalRef.current);
    }
    
    // Set initial value
    setSecondsRemaining(Math.floor(initialSeconds));
    
    // Don't start if already expired
    if (initialSeconds <= 0) {
      onExpireRef.current?.();
      return;
    }
    
    // Start countdown
    intervalRef.current = setInterval(() => {
      setSecondsRemaining((prev) => {
        const next = prev - 1;
        if (next <= 0) {
          if (intervalRef.current) {
            clearInterval(intervalRef.current);
            intervalRef.current = null;
          }
          // Call onExpire after state update
          setTimeout(() => onExpireRef.current?.(), 0);
          return 0;
        }
        return next;
      });
    }, 1000);

    return () => {
      if (intervalRef.current) {
        clearInterval(intervalRef.current);
        intervalRef.current = null;
      }
    };
  }, [initialSeconds]);

  const formatTime = useCallback((seconds: number): string => {
    const hrs = Math.floor(seconds / 3600);
    const mins = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;

    if (hrs > 0) {
      return `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
  }, []);

  const isWarning = secondsRemaining < 300; // Less than 5 minutes
  const isCritical = secondsRemaining < 60; // Less than 1 minute

  return (
    <div
      className={`flex items-center gap-2 px-3 py-1.5 rounded-lg font-mono text-lg font-bold ${
        isCritical 
          ? 'bg-red-500/20 text-red-400 animate-pulse' 
          : isWarning 
            ? 'bg-yellow-500/20 text-yellow-400' 
            : 'bg-primary/20 text-primary'
      }`}
    >
      <Clock size={18} />
      {formatTime(secondsRemaining)}
    </div>
  );
}
