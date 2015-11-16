class AnalysisJob < ActiveJob::Base

  def perform
    tracking_ids = UserAction.select(:tracking_id).uniq
    tracking_ids.each do |t|
      action_count = {}
      actions = UserAction.where(tracking_id: t.tracking_id)
      for action in actions
        if action_count.has_key? action.name
          action_count[action.name] += 1
        else
          action_count[action.name] = 1
        end
      end
      p action_count
    end
    #AnalysisJob.delay_for(1.day).perform_later
  end
end
